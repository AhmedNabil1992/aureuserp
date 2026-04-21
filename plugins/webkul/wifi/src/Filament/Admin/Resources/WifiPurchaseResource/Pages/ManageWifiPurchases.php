<?php

namespace Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Invoice;
use Webkul\Account\Models\MoveLine;
use Webkul\Accounting\Models\Journal;
use Webkul\Partner\Models\Partner;
use Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource;
use Webkul\Wifi\Models\WifiPackage;
use Webkul\Wifi\Models\WifiPartnerCloud;

class ManageWifiPurchases extends ManageRecords
{
    protected static string $resource = WifiPurchaseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('wifi::filament/resources/wifi_purchase.form.buttons.new-purchase'))
                ->icon('heroicon-o-plus-circle')
                ->mutateDataUsing(function (array $data): array {
                    $package = WifiPackage::query()->with('product')->find($data['wifi_package_id'] ?? null);
                    $partner = Partner::query()->find($data['partner_id'] ?? null);

                    if (! $package) {
                        throw ValidationException::withMessages([
                            'wifi_package_id' => __('wifi::filament/resources/wifi_purchase.messages.select_package'),
                        ]);
                    }

                    if (! $partner) {
                        throw ValidationException::withMessages([
                            'partner_id' => __('wifi::filament/resources/wifi_purchase.messages.select_customer'),
                        ]);
                    }

                    if (! $package->currency_id) {
                        throw ValidationException::withMessages([
                            'wifi_package_id' => __('wifi::filament/resources/wifi_purchase.messages.package_currency'),
                        ]);
                    }

                    if (blank($data['cloud_id'] ?? null)) {
                        throw ValidationException::withMessages([
                            'cloud_id' => __('wifi::filament/resources/wifi_purchase.messages.select_cloud'),
                        ]);
                    }

                    $hasCloudMapping = WifiPartnerCloud::query()
                        ->where('partner_id', $partner->id)
                        ->where('cloud_id', $data['cloud_id'])
                        ->exists();

                    if (! $hasCloudMapping) {
                        throw ValidationException::withMessages([
                            'cloud_id' => __('wifi::filament/resources/wifi_purchase.messages.cloud_assigned'),
                        ]);
                    }

                    $journalQuery = Journal::query()
                        ->where('type', JournalType::SALE->value);

                    if ($package->currency_id) {
                        $journalQuery
                            ->where(function ($query) use ($package): void {
                                $query
                                    ->whereNull('currency_id')
                                    ->orWhere('currency_id', $package->currency_id);
                            })
                            ->orderByRaw('CASE WHEN currency_id = ? THEN 0 ELSE 1 END', [$package->currency_id]);
                    }

                    $journal = $journalQuery
                        ->orderBy('id')
                        ->first();

                    if (! $journal) {
                        throw ValidationException::withMessages([
                            'wifi_package_id' => __('wifi::filament/resources/wifi_purchase.messages.no_sales'),
                        ]);
                    }

                    $currencyId = $package->currency_id;

                    if (! $currencyId) {
                        throw ValidationException::withMessages([
                            'wifi_package_id' => __('wifi::filament/resources/wifi_purchase.messages.package_currency'),
                        ]);
                    }

                    $cardsQuantity = max(1, (int) ($data['quantity'] ?? 1));
                    $packageCards = max(1, (int) ($package->quantity ?? 1));
                    $invoiceLineQuantity = max(1, (int) round($cardsQuantity / $packageCards));

                    $priceUnit = (float) ($partner->is_dealer && $package->dealer_amount !== null
                        ? $package->dealer_amount
                        : $package->amount);

                    $invoiceLineId = DB::transaction(function () use ($journal, $currencyId, $partner, $package, $invoiceLineQuantity, $priceUnit): int {
                        $invoice = Invoice::query()->create([
                            'journal_id'            => $journal->id,
                            'company_id'            => $journal->company_id,
                            'currency_id'           => $currencyId,
                            'partner_id'            => $partner->id,
                            'commercial_partner_id' => $partner->id,
                            'invoice_user_id'       => Auth::id(),
                            'state'                 => MoveState::DRAFT,
                            'move_type'             => MoveType::OUT_INVOICE,
                            'date'                  => now()->toDateString(),
                            'invoice_date'          => now()->toDateString(),
                            'invoice_date_due'      => now()->addDays(30)->toDateString(),
                        ]);

                        $moveLine = $invoice->invoiceLines()->create([
                            'product_id' => $package->product_id,
                            'uom_id'     => $package->product?->uom_id,
                            'quantity'   => $invoiceLineQuantity,
                            'price_unit' => $priceUnit,
                        ]);

                        AccountFacade::computeAccountMove($invoice);

                        $invoice = AccountFacade::confirmMove($invoice->refresh());

                        $this->applyOutstandingAdvancePayments($invoice->refresh());

                        return $moveLine->id;
                    });

                    $data['move_line_id'] = $invoiceLineId;

                    unset($data['partner_id']);

                    return $data;
                }),
        ];
    }

    private function applyOutstandingAdvancePayments(Invoice $invoice): void
    {
        $invoice->loadMissing(['paymentTermLines.account']);

        $openTermLines = $invoice->paymentTermLines
            ->filter(fn (MoveLine $line): bool => ! $line->reconciled && (float) $line->amount_residual !== 0.0)
            ->values();

        foreach ($openTermLines as $termLine) {
            $operator = (float) $termLine->amount_residual > 0 ? '<' : '>';

            $outstandingLines = MoveLine::query()
                ->with('move')
                ->where('partner_id', $invoice->partner_id)
                ->where('account_id', $termLine->account_id)
                ->where('parent_state', MoveState::POSTED)
                ->where('reconciled', false)
                ->where('move_id', '!=', $invoice->id)
                ->where('amount_residual', $operator, 0)
                ->whereHas('move', fn ($query) => $query->whereNotNull('origin_payment_id'))
                ->orderBy('date')
                ->orderBy('id')
                ->get();

            if ($outstandingLines->isEmpty()) {
                continue;
            }

            try {
                AccountFacade::reconcile((new EloquentCollection([$termLine]))->merge($outstandingLines));

                $paymentIds = $outstandingLines
                    ->pluck('move.origin_payment_id')
                    ->filter()
                    ->unique()
                    ->values()
                    ->all();

                if (! empty($paymentIds)) {
                    $invoice->matchedPayments()->syncWithoutDetaching($paymentIds);
                }
            } catch (\Throwable $throwable) {
                Log::warning('Wi-Fi purchase auto reconciliation skipped.', [
                    'invoice_id' => $invoice->id,
                    'line_id'    => $termLine->id,
                    'error'      => $throwable->getMessage(),
                ]);
            }
        }
    }
}
