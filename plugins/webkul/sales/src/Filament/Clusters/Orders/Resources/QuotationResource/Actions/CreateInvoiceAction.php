<?php

namespace Webkul\Sale\Filament\Clusters\Orders\Resources\QuotationResource\Actions;

use Closure;
use Exception;
use Filament\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Schema as DatabaseSchema;
use Webkul\PluginManager\Package;
use Webkul\Referral\Services\ReferralService;
use Webkul\Sale\Enums\AdvancedPayment;
use Webkul\Sale\Enums\InvoiceStatus;
use Webkul\Sale\Facades\SaleOrder as SalesFacade;
use Webkul\Sale\Models\Order;

class CreateInvoiceAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'orders.sales.create-invoice';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->color(function (Order $record): string {
                if ($record->invoice_status != InvoiceStatus::TO_INVOICE) {
                    return 'gray';
                }

                return 'primary';
            })
            ->label(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.title'))
            ->schema([
                Radio::make('advance_payment_method')
                    ->inline(false)
                    ->label(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.form.fields.create-invoice'))
                    ->options(function () {
                        $options = AdvancedPayment::options();

                        return Arr::only($options, [
                            AdvancedPayment::DELIVERED->value,
                        ]);
                    })
                    ->default(AdvancedPayment::DELIVERED->value)
                    ->live(),
                TextInput::make('referral_code')
                    ->label(__('referrals::app.fields.code'))
                    ->helperText(__('referrals::app.codes.invoice_help'))
                    ->maxLength(32)
                    ->dehydrateStateUsing(fn (?string $state): ?string => filled($state) ? strtoupper(trim($state)) : null)
                    ->visible(fn (): bool => class_exists(ReferralService::class)
                        && DatabaseSchema::hasColumn('accounts_account_moves', 'referral_code')
                        && Package::isPluginInstalled('referrals')),
                Group::make()
                    ->columns(2)
                    ->schema([
                        TextInput::make('amount')
                            ->visible(fn (Get $get) => $get('advance_payment_method') == AdvancedPayment::PERCENTAGE->value)
                            ->rules('required', 'numeric')
                            ->default(0.00)
                            ->suffix('%'),
                        TextInput::make('amount')
                            ->visible(fn (Get $get) => $get('advance_payment_method') == AdvancedPayment::FIXED->value)
                            ->rules('required', 'numeric')
                            ->default(0.00)
                            ->prefix(fn ($record) => $record->currency->symbol),
                    ]),
            ])
            ->hidden(fn ($record) => $record->invoice_status != InvoiceStatus::TO_INVOICE)
            ->action(function (Order $record, $data) {
                if ($record->qty_to_invoice == 0) {
                    Notification::make()
                        ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.title'))
                        ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.no-invoiceable-lines.body'))
                        ->warning()
                        ->send();

                    return;
                }

                try {
                    SalesFacade::createInvoice($record, $data);
                } catch (Exception $e) {
                    Notification::make()
                        ->danger()
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);

                    return;
                }

                Notification::make()
                    ->title(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.invoice-created.title'))
                    ->body(__('sales::filament/clusters/orders/resources/quotation/actions/create-invoice.notification.invoice-created.body'))
                    ->success()
                    ->send();
            });
    }
}
