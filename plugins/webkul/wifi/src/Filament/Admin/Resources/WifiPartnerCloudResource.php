<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Partner\Models\Partner;
use Webkul\Wifi\Filament\Admin\Resources\WifiPartnerCloudResource\Pages\ManageWifiPartnerClouds;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\WifiPartnerCloud;

class WifiPartnerCloudResource extends Resource
{
    protected static ?string $model = WifiPartnerCloud::class;

    protected static ?string $slug = 'wifi-customer-clouds';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static string|\UnitEnum|null $navigationGroup = 'Wi-Fi';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return 'Customer Clouds';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->label('Customer')
                    ->options(fn (): array => Partner::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('cloud_id')
                    ->label('Cloud')
                    ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('partner.name')
                    ->label('Customer')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cloud.name')
                    ->label('Cloud')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cloud_id')
                    ->label('Cloud Number')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWifiPartnerClouds::route('/'),
        ];
    }
}
