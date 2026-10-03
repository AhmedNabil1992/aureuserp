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
use Illuminate\Database\Eloquent\Builder;
use Webkul\Partner\Models\Partner;
use Webkul\Wifi\Filament\Admin\Resources\WifiPartnerCloudResource\Pages\ManageWifiPartnerClouds;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\WifiPartnerCloud;

class WifiPartnerCloudResource extends Resource
{
    protected static ?string $model = WifiPartnerCloud::class;

    protected static ?string $slug = 'wifi-customer-clouds';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-link';

    protected static ?int $navigationSort = 2;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/resources/wifi_partner_cloud.navigation.title');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('partner_id')
                    ->label(__('wifi::filament/resources/wifi_partner_cloud.form.sections.general.fields.partner_id'))
                    ->options(fn (): array => Partner::query()->orderBy('name')->pluck('name', 'id')->all())
                    ->searchable()
                    ->preload()
                    ->required(),
                Select::make('cloud_id')
                    ->label(__('wifi::filament/resources/wifi_partner_cloud.form.sections.general.fields.cloud_id'))
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
                    ->label(__('wifi::filament/resources/wifi_partner_cloud.table.columns.partner'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('cloud.name')
                    ->label(__('wifi::filament/resources/wifi_partner_cloud.table.columns.cloud'))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $cloudIds = Cloud::query()
                            ->whereLike('name', "%{$search}%")
                            ->pluck('id');

                        return $query->whereIn('cloud_id', $cloudIds);
                    })
                    ->sortable(query: function (Builder $query, string $direction): Builder {
                        $orderedCloudIds = Cloud::query()
                            ->orderBy('name', $direction)
                            ->pluck('id')
                            ->map(static fn (mixed $id): int => (int) $id)
                            ->all();

                        if ($orderedCloudIds === []) {
                            return $query->orderBy('cloud_id', $direction);
                        }

                        $orderCases = collect($orderedCloudIds)
                            ->map(static fn (int $id, int $position): string => "WHEN {$id} THEN {$position}")
                            ->implode(' ');

                        return $query->orderByRaw(
                            'CASE wifi_partner_clouds.cloud_id '.$orderCases.' ELSE '.count($orderedCloudIds).' END'
                        );
                    }),
                TextColumn::make('cloud_id')
                    ->label(__('wifi::filament/resources/wifi_partner_cloud.table.columns.cloud_number'))
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
