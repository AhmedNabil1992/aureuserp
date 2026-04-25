<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Wifi\Filament\Admin\Resources\VoucherResource\Pages\ListVouchers;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\Voucher;

class VoucherResource extends Resource
{
    protected static ?string $model = Voucher::class;

    protected static ?string $slug = 'vouchers';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 6;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/voucher.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('wifi::filament/resources/voucher.table.columns.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('wifi::filament/resources/voucher.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('cloud_id')
                    ->label(__('wifi::filament/resources/voucher.table.columns.cloud_id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('realm')
                    ->label(__('wifi::filament/resources/voucher.table.columns.realm'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('status')
                    ->label(__('wifi::filament/resources/voucher.table.columns.status'))
                    ->badge(),
                Tables\Columns\TextColumn::make('profile')
                    ->label(__('wifi::filament/resources/voucher.table.columns.profile'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('created')
                    ->label(__('wifi::filament/resources/voucher.table.columns.created'))
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modified')
                    ->label(__('wifi::filament/resources/voucher.table.columns.modified'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('cloud_id')
                    ->label(__('wifi::filament/resources/voucher.table.filters.cloud_id'))
                    ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->modifyQueryUsing(fn (Builder $query, array $data): Builder => blank($data['value'])
                        ? $query
                        : $query->where('cloud_id', (int) $data['value'])),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListVouchers::route('/'),
        ];
    }
}
