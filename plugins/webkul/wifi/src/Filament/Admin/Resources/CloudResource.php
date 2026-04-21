<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use Filament\Actions\ViewAction;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables;
use Filament\Tables\Table;
use Webkul\Wifi\Filament\Admin\Resources\CloudResource\Pages;
use Webkul\Wifi\Models\Cloud;

class CloudResource extends Resource
{
    protected static ?string $model = Cloud::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cloud';

    protected static ?int $navigationSort = 1;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return 'Clouds';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->label(__('wifi::filament/resources/cloud.table.columns.id'))
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->label(__('wifi::filament/resources/cloud.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created')
                    ->label(__('wifi::filament/resources/cloud.table.columns.created'))
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modified')
                    ->label(__('wifi::filament/resources/cloud.table.columns.modified'))
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                // ViewAction::make(),
            ])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListClouds::route('/'),
            // 'view'  => Pages\ViewCloud::route('/{record}'),
        ];
    }
}
