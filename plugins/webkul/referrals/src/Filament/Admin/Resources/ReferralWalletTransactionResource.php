<?php

declare(strict_types=1);

namespace Webkul\Referral\Filament\Admin\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Referral\Filament\Admin\Resources\ReferralWalletTransactionResource\Pages\ListReferralWalletTransactions;
use Webkul\Referral\Models\ReferralWalletTransaction;
use Webkul\Support\Enums\NavigationGroup;

class ReferralWalletTransactionResource extends Resource
{
    protected static ?string $model = ReferralWalletTransaction::class;

    protected static ?string $slug = 'referrals/wallet-transactions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-wallet';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string|\UnitEnum
    {
        return NavigationGroup::Referrals;
    }

    public static function getNavigationLabel(): string
    {
        return __('referrals::app.wallet.transactions');
    }

    public static function getModelLabel(): string
    {
        return __('referrals::app.wallet.transaction');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->where('company_id', current_company_id());
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('created_at')->label(__('referrals::app.wallet.date'))->dateTime()->sortable(),
            TextColumn::make('partner.name')->label(__('referrals::app.fields.customer'))->searchable(),
            TextColumn::make('type')->label(__('referrals::app.wallet.type'))
                ->formatStateUsing(fn (string $state): string => __('referrals::app.wallet.types.'.$state))->badge(),
            TextColumn::make('amount')->label(__('referrals::app.wallet.amount'))
                ->money(fn ($record) => $record->currency?->name ?? 'EGP')->sortable(),
            TextColumn::make('invoiceMove.name')->label(__('referrals::app.fields.invoice'))->placeholder('-'),
            TextColumn::make('accountingMove.name')->label(__('referrals::app.wallet.accounting_entry'))->placeholder('-'),
        ])->filters([
            SelectFilter::make('type')->options([
                'earn'            => __('referrals::app.wallet.types.earn'),
                'earn_reversal'   => __('referrals::app.wallet.types.earn_reversal'),
                'redeem'          => __('referrals::app.wallet.types.redeem'),
                'redeem_reversal' => __('referrals::app.wallet.types.redeem_reversal'),
            ]),
        ]);
    }

    public static function getPages(): array
    {
        return ['index' => ListReferralWalletTransactions::route('/')];
    }
}
