<?php

namespace Webkul\Account\Filament\Customer\Resources\BalanceHistoryResource\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Pages\CreateRecord;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Customer\Resources\BalanceHistoryResource;
use Webkul\Account\Models\BalanceRequest;

class CreateBalanceRequest extends CreateRecord
{
    protected static string $resource = BalanceHistoryResource::class;

    public function form(Schema $schema): Schema
    {
        $partnerId = Auth::guard('customer')->id();

        return $schema
            ->schema([
                TextInput::make('amount')
                    ->label(__('المبلغ المطلوب'))
                    ->numeric()
                    ->minValue(0.01)
                    ->maxValue(999999.99)
                    ->step(0.01)
                    ->required()
                    ->helperText(__('أدخل المبلغ الذي تريد إضافته إلى حسابك')),

                Select::make('payment_transaction_id')
                    ->label(__('عملية الدفع'))
                    ->relationship(
                        'paymentTransaction',
                        'payment_reference',
                        fn ($query) => $query
                            ->where('partner_id', $partnerId)
                            ->latest('id')
                    )
                    ->getOptionLabelFromRecordUsing(fn ($record) => "{$record->payment_reference} - {$record->account_number}")
                    ->required()
                    ->searchable()
                    ->helperText(__('اختر عملية الدفع المسجلة في نظام المدفوعات')),
            ]);
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['partner_id'] = Auth::guard('customer')->id();
        $data['status'] = BalanceRequest::STATUS_PENDING;
        $data['requested_at'] = now();

        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return __('تم إنشاء طلب الرصيد بنجاح');
    }
}
