<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\ActionGroup;
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

    protected static ?int $navigationSort = 3;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/wifi_package.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('product_id')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.product_id'))
                    ->options(fn (): array => Product::query()
                        ->where('type', ProductType::SERVICE->value)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->default(fn (): ?int => Product::query()
                        ->where('type', ProductType::SERVICE->value)
                        ->where('name', 'Wi-Fi Voucher')
                        ->value('id'))
                    ->helperText(__('wifi::filament/resources/wifi_package.form.sections.general.fields.product_id_helper_text'))
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('package_type')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.package_type'))
                    ->options(WifiPackageType::class)
                    ->helperText(__('wifi::filament/resources/wifi_package.form.sections.general.fields.package_type_helper_text'))
                    ->required(),
                Select::make('currency_id')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.currency_id'))
                    ->options(fn (): array => Currency::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                TextInput::make('quantity')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.quantity'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->required(),
                TextInput::make('amount')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.amount'))
                    ->numeric()
                    ->minValue(0)
                    ->default(0)
                    ->required(),
                TextInput::make('dealer_amount')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.dealer_amount'))
                    ->numeric()
                    ->minValue(0),
                Toggle::make('is_active')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.is_active'))
                    ->default(true),
                Textarea::make('description')
                    ->label(__('wifi::filament/resources/wifi_package.form.sections.general.fields.description'))
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
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('package_type')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.package_type'))
                    ->badge(),
                TextColumn::make('currency.name')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.currency'))
                    ->sortable(),
                TextColumn::make('quantity')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('amount')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.amount'))
                    ->money(fn (WifiPackage $record): ?string => $record->currency?->name, true)
                    ->sortable(),
                TextColumn::make('dealer_amount')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.dealer_amount'))
                    ->money(fn (WifiPackage $record): ?string => $record->currency?->name, true)
                    ->sortable(),
                IconColumn::make('is_active')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('wifi::filament/resources/wifi_package.table.columns.updated_at'))
                    ->since()
                    ->sortable(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                ]),
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
