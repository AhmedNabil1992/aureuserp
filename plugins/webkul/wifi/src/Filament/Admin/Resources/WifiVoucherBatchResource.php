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
use Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource\Pages\ManageWifiVoucherBatches;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\DynamicClient;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\WifiPurchase;
use Webkul\Wifi\Models\WifiVoucherBatch;

class WifiVoucherBatchResource extends Resource
{
    protected static ?string $model = WifiVoucherBatch::class;

    protected static ?string $slug = 'wifi-voucher-batches';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static string|\UnitEnum|null $navigationGroup = 'Wi-Fi';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return 'Voucher Batches';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('wifi_purchase_id')
                    ->label('Purchase')
                    ->options(fn (): array => WifiPurchase::query()
                        ->with(['package.product', 'invoiceLine.move.partner'])
                        ->orderByDesc('id')
                        ->get()
                        ->mapWithKeys(fn (WifiPurchase $purchase): array => [$purchase->id => $purchase->display_name])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(function (Set $set, $state): void {
                        $purchase = WifiPurchase::query()->find($state);

                        if ($purchase) {
                            $set('cloud_id', $purchase->cloud_id);
                        }
                    })
                    ->required(),
                Select::make('cloud_id')
                    ->label('Cloud')
                    ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->live(),
                Select::make('realm_id')
                    ->label('Realm')
                    ->options(fn (Get $get): array => Realm::query()
                        ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload(),
                Select::make('dynamic_client_id')
                    ->label('Access Point')
                    ->options(fn (Get $get): array => DynamicClient::query()
                        ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId))
                        ->orderBy('name')
                        ->get()
                        ->mapWithKeys(fn (DynamicClient $client): array => [$client->id => ($client->name ?: $client->nasidentifier)])
                        ->all())
                    ->searchable()
                    ->preload(),
                TextInput::make('batch_code')
                    ->label('Batch Code')
                    ->maxLength(255),
                TextInput::make('quantity')
                    ->label('Cards To Generate')
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->required(),
                Toggle::make('never_expire')
                    ->label('Never Expire')
                    ->default(false),
                TextInput::make('caption')
                    ->label('Caption')
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_code')
                    ->label('Batch')
                    ->searchable(),
                TextColumn::make('purchase.invoiceLine.move.partner.name')
                    ->label('Customer')
                    ->searchable(),
                TextColumn::make('purchase.package.product.name')
                    ->label('Service Product')
                    ->searchable(),
                TextColumn::make('cloud.name')
                    ->label('Cloud')
                    ->searchable(),
                TextColumn::make('dynamicClient.name')
                    ->label('Access Point')
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label('Cards')
                    ->sortable(),
                IconColumn::make('never_expire')
                    ->label('Never Expires')
                    ->boolean(),
                TextColumn::make('created_at')
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
            'index' => ManageWifiVoucherBatches::route('/'),
        ];
    }
}
