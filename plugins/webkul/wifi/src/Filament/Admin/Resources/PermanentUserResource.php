<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Wifi\Filament\Admin\Resources\PermanentUserResource\Pages\ListPermanentUsers;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\PermanentUser;

class PermanentUserResource extends Resource
{
    protected static ?string $model = PermanentUser::class;

    protected static ?string $slug = 'permanent-users';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-user-group';

    protected static ?int $navigationSort = 7;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/permanent_user.navigation.title');
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.id'))
                    ->sortable(),
                Tables\Columns\TextColumn::make('username')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.name'))
                    ->searchable(),
                Tables\Columns\TextColumn::make('cloud.name')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.cloud'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('realm')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.realm'))
                    ->toggleable(),
                Tables\Columns\TextColumn::make('profile')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.profile'))
                    ->toggleable(),
                Tables\Columns\IconColumn::make('active')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.active'))
                    ->boolean(),
                Tables\Columns\TextColumn::make('created')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.created'))
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modified')
                    ->label(__('wifi::filament/resources/permanent_user.table.columns.modified'))
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('cloud_id')
                    ->label(__('wifi::filament/resources/permanent_user.table.filters.cloud_id'))
                    ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListPermanentUsers::route('/'),
        ];
    }
}
