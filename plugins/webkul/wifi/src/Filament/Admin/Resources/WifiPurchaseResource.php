<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Models\MoveLine;
use Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource\Pages\ManageWifiPurchases;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\WifiPackage;
use Webkul\Wifi\Models\WifiPurchase;

class WifiPurchaseResource extends Resource
{
    protected static ?string $model = WifiPurchase::class;

    protected static ?string $slug = 'wifi-purchases';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static string|\UnitEnum|null $navigationGroup = 'Wi-Fi';

    protected static ?int $navigationSort = 4;

    public static function getNavigationLabel(): string
    {
        return 'Purchases';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('wifi_package_id')
                    ->label('Wi-Fi Package')
                    ->options(fn (): array => WifiPackage::query()
                        ->with('product')
                        ->orderBy('id', 'desc')
                        ->get()
                        ->mapWithKeys(fn (WifiPackage $package): array => [$package->id => $package->display_name])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => $set('quantity', static::resolveSuggestedQuantity($state, $get('move_line_id'))))
                    ->required(),
                Select::make('move_line_id')
                    ->label('Invoice Line')
                    ->searchable()
                    ->getSearchResultsUsing(fn (string $search): array => static::getMoveLineSearchResults($search))
                    ->getOptionLabelUsing(fn ($value): ?string => static::getMoveLineLabel($value))
                    ->live()
                    ->afterStateUpdated(fn (Get $get, Set $set, $state) => $set('quantity', static::resolveSuggestedQuantity($get('wifi_package_id'), $state)))
                    ->required(),
                Select::make('cloud_id')
                    ->label('Cloud')
                    ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity')
                    ->label('Purchased Cards')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->required(),
                TextInput::make('remaining_quantity')
                    ->label('Remaining Cards')
                    ->disabled()
                    ->dehydrated(false),
                Toggle::make('is_default')
                    ->label('Default Purchase')
                    ->default(false),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('package.product.name')
                    ->label('Service Product')
                    ->searchable(),
                TextColumn::make('invoiceLine.move.name')
                    ->label('Invoice')
                    ->searchable(),
                TextColumn::make('invoiceLine.move.partner.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('cloud.name')
                    ->label('Cloud')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Cards')
                    ->sortable(),
                TextColumn::make('generated_quantity')
                    ->label('Generated')
                    ->sortable(),
                TextColumn::make('remaining_quantity')
                    ->label('Remaining')
                    ->sortable(),
                IconColumn::make('is_default')
                    ->label('Default')
                    ->boolean(),
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
            'index' => ManageWifiPurchases::route('/'),
        ];
    }

    protected static function resolveSuggestedQuantity($packageId, $moveLineId): ?int
    {
        if (! $packageId || ! $moveLineId) {
            return null;
        }

        $package = WifiPackage::query()->find($packageId);
        $moveLine = MoveLine::query()->find($moveLineId);

        if (! $package || ! $moveLine) {
            return null;
        }

        return max(1, (int) round(((float) $moveLine->quantity) * $package->quantity));
    }

    protected static function getMoveLineSearchResults(string $search): array
    {
        return MoveLine::query()
            ->with(['move.partner', 'product'])
            ->whereNotNull('product_id')
            ->where(function ($query) use ($search): void {
                $query
                    ->where('name', 'like', "%{$search}%")
                    ->orWhere('reference', 'like', "%{$search}%")
                    ->orWhere('id', 'like', "%{$search}%");
            })
            ->orderByDesc('id')
            ->limit(50)
            ->get()
            ->mapWithKeys(fn (MoveLine $moveLine): array => [$moveLine->id => static::formatMoveLineLabel($moveLine)])
            ->all();
    }

    protected static function getMoveLineLabel($value): ?string
    {
        if (! $value) {
            return null;
        }

        $moveLine = MoveLine::query()->with(['move.partner', 'product'])->find($value);

        return $moveLine ? static::formatMoveLineLabel($moveLine) : null;
    }

    protected static function formatMoveLineLabel(MoveLine $moveLine): string
    {
        $invoice = $moveLine->move?->name ?? 'Draft';
        $partner = $moveLine->move?->partner?->name ?? 'No customer';
        $product = $moveLine->product?->name ?? 'No product';

        return sprintf('#%d | %s | %s | %s | qty: %s', $moveLine->id, $invoice, $partner, $product, $moveLine->quantity);
    }
}
