<?php

namespace Webkul\Account\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Livewire\Component;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Filament\Resources\BillResource;
use Webkul\Account\Filament\Resources\CreditNoteResource;
use Webkul\Account\Filament\Resources\InvoiceResource;
use Webkul\Account\Filament\Resources\PaymentResource;
use Webkul\Account\Filament\Resources\RefundResource;
use Webkul\Account\Models\MoveLine;
use Webkul\Account\Models\PartialReconcile;

class InvoiceSummary extends Component implements HasActions, HasSchemas
{
    use InteractsWithActions;
    use InteractsWithSchemas;

    public $record = null;

    public $subtotal = 0;

    public $totalDiscount = 0;

    public $totalTax = 0;

    public $grandTotal = 0;

    public $amountTax = 0;

    public $rounding = 0;

    public $currency = null;

    public $reconcilablePayments = null;

    public $reconciledPayments = null;

    public float $referralWalletApplied = 0;

    protected $listeners = [
        'itemUpdated'           => 'refreshSummary',
        'refreshInvoiceSummary' => 'refreshFromRecord',
    ];

    public function refreshSummary($totals)
    {
        $this->subtotal = $totals['subtotal'];
        $this->totalTax = $totals['totalTax'];
        $this->grandTotal = $totals['grandTotal'];
        $this->amountTax = $totals['totalTax'];
        $this->rounding = $totals['rounding'];
    }

    public function refreshFromRecord()
    {
        $this->record?->refresh();
    }

    public function reconcileAction(): Action
    {
        return Action::make('reconcile')
            ->label(__('accounts::filament/resources/invoice.summary.actions.reconcile.label'))
            ->icon('heroicon-o-check-circle')
            ->size('xs')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $lines = MoveLine::where('id', $arguments['lineId'])->get();

                $lines = $lines->merge($this->record->lines->filter(fn ($line) => $line->account_id == $lines->first()->account_id && ! $line->reconciled
                ));

                AccountFacade::reconcile($lines);
            })
            ->after(fn () => $this->js('window.location.reload()'));
    }

    public function unReconcileAction(): Action
    {
        return Action::make('unReconcile')
            ->label(__('accounts::filament/resources/invoice.summary.actions.unreconcile.label'))
            ->icon('heroicon-o-x-circle')
            ->size('xs')
            ->requiresConfirmation()
            ->action(function (array $arguments) {
                $partialReconcile = PartialReconcile::find($arguments['partial_id']);

                AccountFacade::unReconcile($partialReconcile);
            })
            ->after(fn () => $this->js('window.location.reload()'));
    }

    public function getResourceUrl($record): ?string
    {
        return match ($record['move_type']) {
            MoveType::OUT_INVOICE => InvoiceResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::IN_INVOICE  => BillResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::OUT_REFUND  => CreditNoteResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::IN_REFUND   => RefundResource::getUrl('view', ['record' => $record['move_id']]),
            MoveType::ENTRY       => PaymentResource::getUrl('view', ['record' => $record['account_payment_id']]),
        };
    }

    public function render()
    {
        $this->reconcilablePayments = $this->record?->getReconcilablePayments();

        $this->reconciledPayments = $this->record?->getReconciledPayments();

        $this->referralWalletApplied = (float) ($this->record?->referral_wallet_amount ?? 0);

        if (Schema::hasTable('referral_wallet_transactions') && ! empty($this->reconciledPayments['lines'])) {
            $moveIds = collect($this->reconciledPayments['lines'])->pluck('move_id')->filter()->unique();
            $walletMoveIds = DB::table('referral_wallet_transactions')
                ->whereIn('accounting_move_id', $moveIds)->pluck('accounting_move_id')->all();

            $this->reconciledPayments['lines'] = array_values(array_filter(
                $this->reconciledPayments['lines'],
                fn (array $line): bool => ! in_array($line['move_id'], $walletMoveIds, true),
            ));
        }

        if (Schema::hasTable('referral_wallet_transactions') && $this->record?->id) {
            $walletHistory = DB::table('referral_wallet_transactions')
                ->where('invoice_move_id', $this->record->id)
                ->selectRaw('COUNT(*) as transaction_count, COALESCE(SUM(amount), 0) as net_amount')
                ->first();

            if ((int) ($walletHistory->transaction_count ?? 0) > 0) {
                $this->referralWalletApplied = max(0, -(float) $walletHistory->net_amount);
            }
        }

        return view('accounts::livewire/invoice-summary');
    }
}
