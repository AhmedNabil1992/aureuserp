<?php

namespace Webkul\Account\Filament\Resources\InvoiceResource\Actions;

use Closure;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Livewire\Component;
use Throwable;
use Webkul\Account\Enums\AutoPost;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Move;
use Webkul\PluginManager\Package;
use Webkul\Referral\Services\ReferralService;

class ConfirmAction extends Action
{
    protected bool|Closure $hasDatabaseTransactions = true;

    public static function getDefaultName(): ?string
    {
        return 'customers.invoice.confirm';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('accounts::filament/resources/invoice/actions/confirm-action.title'))
            ->color('primary')
            ->action(function (Move $record, Component $livewire): void {
                $record->checked = $record->journal->auto_check_on_post;

                try {
                    if (
                        filled($record->referral_code)
                        && class_exists(ReferralService::class)
                        && Package::isPluginInstalled('referrals')
                    ) {
                        app(ReferralService::class)->applyToDraftMove(
                            $record,
                            $record->referral_code,
                            ReferralService::CONTEXT_SALES_INVOICE,
                            Move::class,
                            $record->id,
                        );
                    }

                    $record = AccountFacade::confirmMove($record);

                    $livewire->refreshFormData(['state', 'parent_state']);

                    $livewire->dispatch('refreshInvoiceSummary');
                } catch (Throwable $e) {
                    Notification::make()
                        ->warning()
                        ->title(__('accounts::filament/resources/invoice/actions/confirm-action.notification.error.title'))
                        ->body($e->getMessage())
                        ->send();

                    $this->halt(shouldRollBackDatabaseTransaction: true);
                }
            })
            ->hidden(function (Move $record) {
                return
                    $record->state !== MoveState::DRAFT
                    || (
                        $record->auto_post !== AutoPost::NO
                        && $record->date > now()
                    );
            });
    }
}
