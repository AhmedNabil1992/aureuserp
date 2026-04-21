<?php

namespace Webkul\Wifi\Filament\Admin\Resources;

use BackedEnum;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Models\MoveLine;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\Currency;
use Webkul\Wifi\Filament\Admin\Resources\WifiPurchaseResource\Pages\ManageWifiPurchases;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\WifiPackage;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Wifi\Models\WifiPurchase;

class WifiPurchaseResource extends Resource
{
    protected static ?string $model = WifiPurchase::class;

    protected static ?string $slug = 'wifi-purchases';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-receipt-percent';

    protected static ?int $navigationSort = 4;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/wifi_purchase.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->label(__('wifi::filament/resources/wifi_purchase.form.sections.general.fields.partner_id'))
                    ->options(fn (): array => Partner::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->live()
                    ->helperText(function (Get $get): string {
                        $partnerId = $get('partner_id');

                        if (! $partnerId) {
                            return __('wifi::filament/resources/wifi_purchase.form.sections.general.helper.partner_id');
                        }

                        return sprintf(__('wifi::filament/resources/wifi_purchase.form.sections.general.helper.partner_id'), static::resolvePartnerAvailableCreditLabel((int) $partnerId));
                    })
                    ->afterStateUpdated(function (Set $set, $state): void {
                        $cloudIds = WifiPartnerCloud::query()
                            ->where('partner_id', $state)
                            ->pluck('cloud_id')
                            ->values();

                        if ($cloudIds->count() === 1) {
                            $set('cloud_id', $cloudIds->first());

                            return;
                        }

                        $set('cloud_id', null);
                    })
                    ->required(),
                Select::make('wifi_package_id')
                    ->label(__('wifi::filament/resources/wifi_purchase.form.sections.general.fields.wifi_package_id'))
                    ->options(fn (): array => WifiPackage::query()
                        ->with('product')
                        ->orderBy('id', 'desc')
                        ->get()
                        ->mapWithKeys(fn (WifiPackage $package): array => [$package->id => $package->display_name])
                        ->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('cloud_id')
                    ->label(__('wifi::filament/resources/wifi_purchase.form.sections.general.fields.cloud_id'))
                    ->helperText(__('wifi::filament/resources/wifi_purchase.form.sections.general.helper.cloud_id'))
                    ->options(function (Get $get): array {
                        $partnerId = $get('partner_id');

                        if (! $partnerId) {
                            return [];
                        }

                        $cloudIds = WifiPartnerCloud::query()
                            ->where('partner_id', $partnerId)
                            ->pluck('cloud_id')
                            ->all();

                        if (empty($cloudIds)) {
                            return [];
                        }

                        return Cloud::query()
                            ->whereIn('id', $cloudIds)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();
                    })
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
                TextColumn::make('package.product.name')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.service_product'))
                    ->searchable(),
                TextColumn::make('invoiceLine.move.name')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.invoice'))
                    ->searchable(),
                TextColumn::make('invoiceLine.move.partner.name')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.partner'))
                    ->searchable(),
                TextColumn::make('cloud.name')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.cloud'))
                    ->searchable(),
                TextColumn::make('quantity')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.quantity'))
                    ->sortable(),
                TextColumn::make('generated_quantity')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.generated_quantity'))
                    ->sortable(),
                TextColumn::make('remaining_quantity')
                    ->label(__('wifi::filament/resources/wifi_purchase.table.columns.remaining_quantity'))
                    ->sortable(),
            ])
            ->recordActions([])
            ->toolbarActions([]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageWifiPurchases::route('/'),
        ];
    }

    protected static function resolvePartnerAvailableCreditLabel(int $partnerId): string
    {
        $creditRows = MoveLine::query()
            ->where('partner_id', $partnerId)
            ->where('parent_state', MoveState::POSTED)
            ->where('reconciled', false)
            ->where('amount_residual', '<', 0)
            ->whereHas('account', fn ($query) => $query->where('account_type', 'asset_receivable'))
            ->selectRaw('currency_id, SUM(CASE WHEN amount_residual_currency != 0 THEN amount_residual_currency ELSE amount_residual END) as residual_total')
            ->groupBy('currency_id')
            ->get();

        if ($creditRows->isEmpty()) {
            return '0.00';
        }

        $currencyNames = Currency::query()
            ->whereIn('id', $creditRows->pluck('currency_id')->filter()->all())
            ->pluck('name', 'id');

        $formattedCredits = $creditRows
            ->filter(fn ($row): bool => (float) $row->residual_total < 0)
            ->map(function ($row) use ($currencyNames): string {
                $currencyName = $currencyNames->get($row->currency_id);

                if ($currencyName) {
                    return money(abs((float) $row->residual_total), $currencyName);
                }

                return number_format(abs((float) $row->residual_total), 2);
            })
            ->values();

        if ($formattedCredits->isEmpty()) {
            return '0.00';
        }

        return $formattedCredits->implode(' + ');
    }
}
