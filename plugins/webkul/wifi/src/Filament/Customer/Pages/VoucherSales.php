<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Filament\Customer\Concerns\HasWifiAccess;
use Webkul\Wifi\Models\VoucherSale;

class VoucherSales extends Page implements HasTable
{
    use HasCustomerCloudAccess, HasWifiAccess, InteractsWithTable;

    protected string $view = 'wifi::filament.customer.pages.voucher-sales';

    protected static ?int $navigationSort = 5;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-chart-bar';

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/voucher-sales.title');
    }

    public static function getmodelLabel(): string
    {
        return __('wifi::filament/customer/pages/voucher-sales.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public function table(Table $table): Table
    {
        $cloudIds = $this->getCustomerCloudIds();

        if (empty($cloudIds)) {
            return $table
                ->query(VoucherSale::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading(__('wifi::filament/customer/pages/voucher-sales.empty'))
                ->emptyStateIcon('heroicon-o-document-chart-bar');
        }

        $query = VoucherSale::query()
            ->select(
                DB::raw('MIN(sales.ID) as id'),
                'clouds.name as CloudName',
                'dynamic_clients.name as AccessName',
                DB::raw('SUM(CASE WHEN DATE(sales.Date) = CURDATE() THEN sales.SCount ELSE 0 END) AS Today'),
                DB::raw('SUM(CASE WHEN WEEK(DATE(sales.Date), 1) = WEEK(CURDATE(), 1) AND YEAR(DATE(sales.Date)) = YEAR(CURDATE()) THEN sales.SCount ELSE 0 END) AS ThisWeek'),
                DB::raw('SUM(CASE WHEN MONTH(DATE(sales.Date)) = MONTH(CURDATE()) AND YEAR(DATE(sales.Date)) = YEAR(CURDATE()) THEN sales.SCount ELSE 0 END) AS ThisMonth')
            )
            ->join('clouds', 'sales.cloudID', '=', 'clouds.id')
            ->join('dynamic_clients', 'sales.nasidentifier', '=', 'dynamic_clients.nasidentifier')
            ->whereIn('sales.cloudID', $cloudIds)
            ->groupBy('clouds.name', 'dynamic_clients.name');

        return $table
            ->query($query)
            ->emptyStateHeading(__('wifi::filament/customer/pages/voucher-sales.empty'))
            ->emptyStateIcon('heroicon-o-document-chart-bar')
            ->columns([
                TextColumn::make('CloudName')
                    ->label(__('wifi::filament/customer/pages/voucher-sales.columns.cloud_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('AccessName')
                    ->label(__('wifi::filament/customer/pages/voucher-sales.columns.access_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('Today')
                    ->label(__('wifi::filament/customer/pages/voucher-sales.columns.today'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),

                TextColumn::make('ThisWeek')
                    ->label(__('wifi::filament/customer/pages/voucher-sales.columns.this_week'))
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('ThisMonth')
                    ->label(__('wifi::filament/customer/pages/voucher-sales.columns.this_month'))
                    ->badge()
                    ->color('success')
                    ->sortable(),
            ])
            ->defaultPaginationPageOption(25);
    }

    public function getTableRecordKey(Model|array $record): string
    {
        if (is_array($record)) {
            return (string) ($record['id'] ?? uniqid());
        }

        return (string) ($record->id ?? $record->getKey() ?? uniqid());
    }
}
