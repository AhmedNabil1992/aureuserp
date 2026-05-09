<?php

namespace Webkul\Account\Filament\Customer\Resources;

use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Account\Filament\Customer\Resources\BalanceHistoryResource\Pages\CreateBalanceRequest;
use Webkul\Account\Filament\Customer\Resources\BalanceHistoryResource\Pages\ListBalanceRequests;
use Webkul\Account\Models\BalanceRequest;

class BalanceHistoryResource extends Resource
{
    protected static ?string $model = BalanceRequest::class;

    protected static ?string $slug = 'balance-history';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-bank-notes';

    protected static ?int $navigationSort = 10;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return __('سجل الرصيد');
    }

    public static function getModelLabel(): string
    {
        return __('طلب الرصيد');
    }

    public static function getPluralModelLabel(): string
    {
        return __('طلبات الرصيد');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('amount')
                    ->label(__('المبلغ'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->formatStateUsing(fn ($state) => number_format($state, 2)),

                TextColumn::make('paymentTransaction.payment_reference')
                    ->label(__('مرجع العملية'))
                    ->sortable()
                    ->placeholder(__('غير متاح')),

                TextColumn::make('status')
                    ->label(__('الحالة'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => BalanceRequest::getStatuses()[$state] ?? $state)
                    ->color(fn ($state) => match ($state) {
                        BalanceRequest::STATUS_PENDING  => 'warning',
                        BalanceRequest::STATUS_APPROVED => 'success',
                        BalanceRequest::STATUS_REJECTED => 'danger',
                        default                         => 'gray',
                    })
                    ->sortable(),

                TextColumn::make('requested_at')
                    ->label(__('تاريخ الطلب'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable(),

                TextColumn::make('approved_at')
                    ->label(__('تاريخ الموافقة'))
                    ->dateTime('Y-m-d H:i')
                    ->sortable()
                    ->placeholder(__('قيد الانتظار')),

                TextColumn::make('rejection_reason')
                    ->label(__('سبب الرفض'))
                    ->visible(fn ($record) => $record->status === BalanceRequest::STATUS_REJECTED)
                    ->limit(50),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('الحالة'))
                    ->options(BalanceRequest::getStatuses()),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('requested_at', 'desc')
            ->modifyQueryUsing(function (Builder $query): Builder {
                $partnerId = Auth::guard('customer')->id();

                return $query->where('partner_id', $partnerId);
            });
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBalanceRequests::route('/'),
            'create' => CreateBalanceRequest::route('/create'),
        ];
    }
}
