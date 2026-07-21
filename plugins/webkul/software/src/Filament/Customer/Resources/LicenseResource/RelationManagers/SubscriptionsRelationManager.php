<?php

namespace Webkul\Software\Filament\Customer\Resources\LicenseResource\RelationManagers;

use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;

class SubscriptionsRelationManager extends RelationManager
{
    protected static string $relationship = 'subscriptions';

    public static function getTitle(Model $ownerRecord, string $pageClass): string
    {
        return __('software::filament/customer/license.pages.view.subscriptions.title') ?: 'الاشتراكات والخدمات المفعلة';
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('feature.name')
                    ->label(__('software::filament/customer/license.pages.view.subscriptions.columns.feature_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('service_type')
                    ->label(__('software::filament/customer/license.pages.view.subscriptions.columns.service_type'))
                    ->badge(),

                TextColumn::make('start_date')
                    ->label(__('software::filament/customer/license.pages.view.subscriptions.columns.start_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('end_date')
                    ->label(__('software::filament/customer/license.pages.view.subscriptions.columns.end_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('is_active')
                    ->label(__('software::filament/customer/license.pages.view.subscriptions.columns.status'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state
                        ? __('software::filament/customer/license.statuses.active')
                        : __('software::filament/customer/license.statuses.inactive'))
                    ->color(fn ($state) => $state ? 'success' : 'gray'),
            ])
            ->paginated([10, 25])
            ->defaultSort('start_date', 'desc');
    }
}
