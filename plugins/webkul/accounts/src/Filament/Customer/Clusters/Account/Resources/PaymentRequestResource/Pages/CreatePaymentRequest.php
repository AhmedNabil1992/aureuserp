<?php

namespace Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource\Pages;

use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentStatus;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Filament\Customer\Clusters\Account\Resources\PaymentRequestResource;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Partner;
use Webkul\Account\Models\Payment;

class CreatePaymentRequest extends CreateRecord
{
    protected static string $resource = PaymentRequestResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $partner = Partner::query()
            ->with('propertyInboundPaymentMethodLine')
            ->find(Auth::guard('customer')->id());

        $journal = Journal::query()
            ->with('company')
            ->where('type', JournalType::BANK)
            ->orderBy('id')
            ->first();

        if (! $partner) {
            throw ValidationException::withMessages([
                'amount' => __('accounts::filament/customer/payment-request.validation.partner_not_found'),
            ]);
        }

        if (! $journal || ! $journal->company?->currency_id) {
            throw ValidationException::withMessages([
                'amount' => __('accounts::filament/customer/payment-request.validation.bank_journal_not_available'),
            ]);
        }

        $payment = new Payment;
        $payment->payment_type = PaymentType::RECEIVE;
        $payment->journal_id = $journal->id;
        $payment->setRelation('journal', $journal);
        $payment->partner_id = $partner->id;
        $payment->setRelation('partner', $partner);
        $payment->computePaymentMethodLineId();

        if (! $payment->payment_method_line_id) {
            throw ValidationException::withMessages([
                'amount' => __('accounts::filament/customer/payment-request.validation.payment_method_not_available'),
            ]);
        }

        return $data + [
            'partner_id'             => $partner->id,
            'journal_id'             => $journal->id,
            'currency_id'            => $journal->company->currency_id,
            'payment_type'           => PaymentType::RECEIVE->value,
            'partner_type'           => 'customer',
            'state'                  => PaymentStatus::DRAFT->value,
            'payment_method_line_id' => $payment->payment_method_line_id,
        ];
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
            ->success()
            ->title(__('accounts::filament/customer/payment-request.notifications.created.title'))
            ->body(__('accounts::filament/customer/payment-request.notifications.created.body'));
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->getRecord()]);
    }
}
