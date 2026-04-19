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

    protected static string|\UnitEnum|null $navigationGroup = 'Wi-Fi';

    protected static ?int $navigationSort = 1;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->numeric()
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('created')
                    ->since()
                    ->sortable(),
                Tables\Columns\TextColumn::make('modified')
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
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
