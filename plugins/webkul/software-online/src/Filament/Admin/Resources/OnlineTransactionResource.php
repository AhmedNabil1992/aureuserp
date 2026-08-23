<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\TransactionType;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineTransactionResource\Pages\ListOnlineTransactions;
use Webkul\SoftwareOnline\Models\OnlineInstanceTransaction;

class OnlineTransactionResource extends Resource
{
    protected static ?string $model = OnlineInstanceTransaction::class;

    protected static ?string $slug = 'online-transactions';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-credit-card';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/admin/resources/transaction.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('software-online::filament/admin/resources/transaction.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software-online::filament/admin/resources/transaction.models.plural');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('instance.instance_number')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.instance'))
                    ->prefix('#')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('partner.name')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.customer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.type'))
                    ->badge(),
                TextColumn::make('billing_cycle')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.billing_cycle'))
                    ->badge(),
                TextColumn::make('amount')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.amount'))
                    ->money('EGP')
                    ->sortable(),
                TextColumn::make('period_start')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.period_start'))
                    ->date(),
                TextColumn::make('period_end')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.period_end'))
                    ->date(),
                TextColumn::make('created_at')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.created_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('type')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.type'))
                    ->options(TransactionType::class),
                SelectFilter::make('billing_cycle')
                    ->label(__('software-online::filament/admin/resources/transaction.fields.billing_cycle'))
                    ->options(BillingCycle::class),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOnlineTransactions::route('/'),
        ];
    }
}
