<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\Currency;
use Webkul\Wifi\Enums\WifiPackageType;
use Webkul\Wifi\Filament\Admin\Resources\WifiPackageResource\Pages\ManageWifiPackages;
use Webkul\Wifi\Models\WifiPackage;

class WifiPackageResource extends Resource
{
    protected static ?string $model = WifiPackage::class;

    protected static ?string $slug = 'wifi-packages';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static string|\UnitEnum|null $navigationGroup = 'Wi-Fi';

    protected static ?int $navigationSort = 3;

    public static function getNavigationLabel(): string
    {
        return 'Packages';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label('Service Product')
                    ->options(fn (): array => Product::query()
                        ->where('type', ProductType::SERVICE->value)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->default(fn (): ?int => Product::query()
                        ->where('type', ProductType::SERVICE->value)
                        ->where('name', 'Wi-Fi Voucher')
                        ->value('id'))
                    ->helperText('Recommended: keep all Wi-Fi packages linked to a single service product (Wi-Fi Voucher).')
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('package_type')
                    ->label('Package Type')
                    ->options(WifiPackageType::class)
                    ->helperText('Use Unlimited for open validity packages and Limited for time-bound packages.')
                    ->required(),
                Select::make('currency_id')
                    ->label('Currency')
                    ->options(fn (): array => Currency::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->label('Cards Per Unit')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->required(),
                TextInput::make('amount')
                    ->label('Sell Amount')
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('dealer_amount')
                    ->label('Dealer Amount')
                    ->numeric()
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Textarea::make('description')
                    ->label('Description')
                    ->rows(3)
                    ->columnSpanFull(),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label('Service Product')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('package_type')
                    ->badge(),
                TextColumn::make('currency.name')
                    ->label('Currency')
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label('Cards Per Unit')
                    ->sortable(),
                TextColumn::make('amount')
                    ->money(fn (WifiPackage $record): ?string => $record->currency?->name, true)
                    ->sortable(),
                TextColumn::make('dealer_amount')
                    ->money(fn (WifiPackage $record): ?string => $record->currency?->name, true)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->since()
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
            'index' => ManageWifiPackages::route('/'),
        ];
    }
}
