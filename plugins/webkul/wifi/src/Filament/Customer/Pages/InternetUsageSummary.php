<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Carbon\Carbon;
use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Wifi\Filament\Customer\Concerns\HasCustomerCloudAccess;
use Webkul\Wifi\Filament\Customer\Concerns\HasWifiAccess;
use Webkul\Wifi\Models\PermanentUser;
use Webkul\Wifi\Models\Radacct;
use Webkul\Wifi\Models\Voucher;

class InternetUsageSummary extends Page implements HasTable
{
    use HasCustomerCloudAccess, HasWifiAccess, InteractsWithTable;

    protected string $view = 'wifi::filament.customer.pages.internet-usage-summary';

    protected static ?int $navigationSort = 3;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    public string $activeTab = 'all';

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/internet-usage-summary.title');
    }

    public static function getmodelLabel(): string
    {
        return __('wifi::filament/customer/pages/internet-usage-summary.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;

        if (method_exists($this, 'resetPage')) {
            $this->resetPage();
        }
    }

    public function getTypeCounts(): array
    {
        $cloudIds = $this->getCustomerCloudIds();
        $realmNames = $this->getCustomerRealmNames();

        if ($realmNames->isEmpty()) {
            return ['all' => 0, 'voucher' => 0, 'user' => 0];
        }

        $periodFilter = $this->tableFilters['period'] ?? [];

        $startDate = ! empty($periodFilter['start_date'])
            ? Carbon::parse($periodFilter['start_date'])->startOfDay()
            : Carbon::now()->startOfMonth();

        $endDate = ! empty($periodFilter['end_date'])
            ? Carbon::parse($periodFilter['end_date'])->endOfDay()
            : Carbon::now();

        $base = Radacct::whereIn('realm', $realmNames)
            ->whereBetween('acctstarttime', [$startDate, $endDate])
            ->whereNotNull('username')
            ->where('username', '!=', '');

        $allCount = (clone $base)->distinct('username')->count('username');

        $voucherCount = (clone $base)
            ->whereIn('username', Voucher::whereIn('cloud_id', $cloudIds)->select('name'))
            ->distinct('username')
            ->count('username');

        $userCount = (clone $base)
            ->whereIn('username', PermanentUser::whereIn('cloud_id', $cloudIds)->select('username'))
            ->distinct('username')
            ->count('username');

        return [
            'all'     => $allCount,
            'voucher' => $voucherCount,
            'user'    => $userCount,
        ];
    }

    public function table(Table $table): Table
    {
        $cloudIds = $this->getCustomerCloudIds();
        $realmNames = $this->getCustomerRealmNames();

        if ($realmNames->isEmpty()) {
            return $table
                ->query(Radacct::query()->whereRaw('1 = 0'))
                ->columns([])
                ->emptyStateHeading(__('wifi::filament/customer/pages/internet-usage-summary.empty.heading'))
                ->emptyStateDescription(__('wifi::filament/customer/pages/internet-usage-summary.empty.description'))
                ->emptyStateIcon('heroicon-o-chart-bar');
        }

        $query = Radacct::query()
            ->selectRaw('MIN(radacctid) as radacctid')
            ->selectRaw('username')
            ->selectRaw('COUNT(*) as sessions_count')
            ->selectRaw('SUM(COALESCE(acctsessiontime, 0)) as total_session_seconds')
            ->selectRaw('SUM(COALESCE(acctinputoctets, 0)) as total_input_octets')
            ->selectRaw('SUM(COALESCE(acctoutputoctets, 0)) as total_output_octets')
            ->selectRaw('SUM(COALESCE(acctinputoctets, 0) + COALESCE(acctoutputoctets, 0)) as total_octets')
            ->selectRaw('MAX(acctstarttime) as last_session_at')
            ->whereIn('realm', $realmNames)
            ->whereNotNull('username')
            ->where('username', '!=', '');

        // Tab filtering
        if ($this->activeTab === 'voucher') {
            $query->whereIn('username', Voucher::whereIn('cloud_id', $cloudIds)->select('name'));
        } elseif ($this->activeTab === 'user') {
            $query->whereIn('username', PermanentUser::whereIn('cloud_id', $cloudIds)->select('username'));
        }

        $query->groupBy('username');

        return $table
            ->query($query)
            ->defaultSort('total_octets', 'desc')
            ->emptyStateHeading(__('wifi::filament/customer/pages/internet-usage-summary.empty.heading'))
            ->emptyStateDescription(__('wifi::filament/customer/pages/internet-usage-summary.empty.description'))
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->columns([
                TextColumn::make('username')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.username'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('type')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.type'))
                    ->badge()
                    ->getStateUsing(function ($record) use ($cloudIds) {
                        static $voucherNames = null;
                        if ($voucherNames === null) {
                            $voucherNames = Voucher::whereIn('cloud_id', $cloudIds)->pluck('name')->flip();
                        }

                        return isset($voucherNames[$record->username])
                            ? __('wifi::filament/customer/pages/internet-usage-summary.tabs.voucher')
                            : __('wifi::filament/customer/pages/internet-usage-summary.tabs.user');
                    })
                    ->color(fn ($state) => $state === __('wifi::filament/customer/pages/internet-usage-summary.tabs.voucher') ? 'info' : 'warning'),

                TextColumn::make('sessions_count')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.sessions_count'))
                    ->badge()
                    ->sortable(),

                TextColumn::make('total_session_seconds')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.total_session_seconds'))
                    ->formatStateUsing(fn ($state) => static::formatSeconds((int) $state))
                    ->sortable(),

                TextColumn::make('total_input_octets')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.total_input_octets'))
                    ->formatStateUsing(fn ($state) => static::formatBytes((float) $state))
                    ->sortable(),

                TextColumn::make('total_output_octets')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.total_output_octets'))
                    ->formatStateUsing(fn ($state) => static::formatBytes((float) $state))
                    ->sortable(),

                TextColumn::make('total_octets')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.total_octets'))
                    ->badge()
                    ->color('success')
                    ->formatStateUsing(fn ($state) => static::formatBytes((float) $state))
                    ->sortable(),

                TextColumn::make('last_session_at')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.columns.last_session_at'))
                    ->dateTime()
                    ->since()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('period')
                    ->label(__('wifi::filament/customer/pages/internet-usage-summary.filters.period'))
                    ->form([
                        DatePicker::make('start_date')
                            ->label(__('wifi::filament/customer/pages/internet-usage-summary.filters.start_date'))
                            ->default(now()->startOfMonth()),

                        DatePicker::make('end_date')
                            ->label(__('wifi::filament/customer/pages/internet-usage-summary.filters.end_date'))
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['start_date'],
                                fn (Builder $query, $date): Builder => $query->where('acctstarttime', '>=', Carbon::parse($date)->startOfDay()),
                            )
                            ->when(
                                $data['end_date'],
                                fn (Builder $query, $date): Builder => $query->where('acctstarttime', '<=', Carbon::parse($date)->endOfDay()),
                            );
                    }),
            ])
            ->defaultPaginationPageOption(25);
    }

    public static function formatBytes(float $bytes): string
    {
        if ($bytes <= 0) {
            return '0 MB';
        }

        if ($bytes >= 1_073_741_824) {
            return number_format($bytes / 1_073_741_824, 2) . ' GB';
        }

        return number_format($bytes / 1_048_576, 2) . ' MB';
    }

    public static function formatSeconds(int $seconds): string
    {
        $hours = intdiv($seconds, 3600);
        $minutes = intdiv($seconds % 3600, 60);

        $hoursLabel = __('wifi::filament/customer/pages/internet-usage-summary.units.hours');
        $minutesLabel = __('wifi::filament/customer/pages/internet-usage-summary.units.minutes');

        if ($hours > 0) {
            return "{$hours} {$hoursLabel} {$minutes} {$minutesLabel}";
        }

        return "{$minutes} {$minutesLabel}";
    }
}
