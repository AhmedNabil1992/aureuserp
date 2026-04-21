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
use Filament\Schemas\Components\Fieldset;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Wifi\Enums\WifiPackageType;
use Webkul\Wifi\Filament\Admin\Resources\WifiVoucherBatchResource\Pages\ManageWifiVoucherBatches;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\DynamicClient;
use Webkul\Wifi\Models\DynamicClientRealm;
use Webkul\Wifi\Models\Profile;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\WifiPurchase;
use Webkul\Wifi\Models\WifiVoucherBatch;

class WifiVoucherBatchResource extends Resource
{
    protected static ?string $model = WifiVoucherBatch::class;

    protected static ?string $slug = 'wifi-voucher-batches';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-ticket';

    protected static ?int $navigationSort = 5;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/wifi_voucher_batch.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('wifi_purchase_id')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.wifi_purchase_id'))
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
                        $purchase = WifiPurchase::query()->with('package')->find($state);

                        if ($purchase) {
                            $set('cloud_id', $purchase->cloud_id);
                            $set('realm_id', null);
                            $set('nasidentifier', null);

                            $isUnlimitedPackage = ($purchase->package?->package_type?->value) === WifiPackageType::Unlimited->value;

                            $set('never_expire', $isUnlimitedPackage);
                        }
                    })
                    ->required(),
                Select::make('cloud_id')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.cloud_id'))
                    ->options(fn (): array => Cloud::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->disabled()
                    ->dehydrated()
                    ->live(),
                Select::make('realm_id')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.realm_id'))
                    ->options(fn (Get $get): array => Realm::query()
                        ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set) => $set('nasidentifier', null)),
                Select::make('nasidentifier')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.nasidentifier'))
                    ->options(function (Get $get): array {
                        $realmId = $get('realm_id');

                        if (blank($realmId)) {
                            return [];
                        }

                        return DynamicClient::query()
                            ->whereIn(
                                'id',
                                DynamicClientRealm::query()
                                    ->where('realm_id', $realmId)
                                    ->pluck('dynamic_client_id')
                            )
                            ->orderBy('name')
                            ->get()
                            ->mapWithKeys(fn (DynamicClient $client): array => [$client->nasidentifier => ($client->name ? ($client->name.' ('.$client->nasidentifier.')') : $client->nasidentifier)])
                            ->all();
                    })
                    ->disabled(fn (Get $get): bool => blank($get('realm_id')))
                    ->searchable()
                    ->preload(),
                Select::make('profile_id')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.profile_id'))
                    ->options(fn (Get $get): array => Profile::query()
                        ->when($get('cloud_id'), fn ($query, $cloudId) => $query->where('cloud_id', $cloudId)->orWhere('cloud_id', -1))
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all())
                    ->searchable()
                    ->preload(),
                TextInput::make('quantity')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.quantity'))
                    ->numeric()
                    ->integer()
                    ->minValue(1)
                    ->maxValue(fn (Get $get, ?WifiVoucherBatch $record): ?int => self::resolveAvailableQuantity($get, $record))
                    ->helperText(function (Get $get, ?WifiVoucherBatch $record): ?string {
                        $availableQuantity = self::resolveAvailableQuantity($get, $record);

                        if ($availableQuantity === null) {
                            return null;
                        }

                        return __('Max available: :quantity', ['quantity' => $availableQuantity]);
                    })
                    ->required(),
                Fieldset::make(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.validity'))
                    ->schema([
                        TextInput::make('days_valid')
                            ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.days_valid'))
                            ->numeric()
                            ->integer()
                            ->default(0)
                            ->minValue(0),
                        TextInput::make('hours_valid')
                            ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.hours_valid'))
                            ->numeric()
                            ->integer()
                            ->default(0)
                            ->minValue(0),
                        TextInput::make('minutes_valid')
                            ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.minutes_valid'))
                            ->numeric()
                            ->integer()
                            ->default(0)
                            ->minValue(0),
                    ])
                    ->columns(3)
                    ->columnSpanFull(),
                Toggle::make('never_expire')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.never_expire'))
                    ->helperText(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.never_expire_helper_text'))
                    ->disabled()
                    ->dehydrated()
                    ->default(false),
                TextInput::make('caption')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.form.sections.general.fields.caption'))
                    ->maxLength(255),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('batch_code')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.batch_code'))
                    ->searchable(),
                TextColumn::make('purchase.invoiceLine.move.partner.name')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.customer'))
                    ->searchable(),
                TextColumn::make('purchase.package.product.name')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.service_product'))
                    ->searchable(),
                TextColumn::make('cloud.name')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.cloud'))
                    ->searchable(),
                TextColumn::make('nasidentifier')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.access_point'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.quantity'))
                    ->sortable(),
                IconColumn::make('never_expire')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.never_expire'))
                    ->boolean(),
                TextColumn::make('created_at')
                    ->label(__('wifi::filament/resources/wifi_voucher_batch.table.columns.created_at'))
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

    private static function resolveAvailableQuantity(Get $get, ?WifiVoucherBatch $record): ?int
    {
        $purchaseId = $get('wifi_purchase_id');

        if (blank($purchaseId)) {
            return null;
        }

        $purchase = WifiPurchase::query()->find($purchaseId);

        if (! $purchase) {
            return null;
        }

        $availableQuantity = (int) $purchase->remaining_quantity;

        if ($record?->exists && (int) $record->wifi_purchase_id === (int) $purchase->id) {
            $availableQuantity += (int) $record->quantity;
        }

        return max(1, $availableQuantity);
    }
}
