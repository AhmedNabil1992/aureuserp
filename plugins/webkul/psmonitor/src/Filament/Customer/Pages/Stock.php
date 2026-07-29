<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Forms\Components\Select;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\Stock as StockModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class Stock extends Page implements Tables\Contracts\HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.stock';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 16;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/stock.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/stock.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('psmonitor::filament/customer/navigation.group');
    }

    public function getHeaderWidgets(): array
    {
        return [
            LicenseSelectorWidget::class,
        ];
    }

    public function getTableEmptyStateHeading(): ?string
    {
        return __('psmonitor::filament/customer/pages/stock.table.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/stock.table.empty_state.description');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = StockModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = StockModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('item.group.Cate_Name')
                    ->label(__('psmonitor::filament/customer/pages/stock.table.columns.category'))
                    ->sortable(),

                TextColumn::make('Barcode')
                    ->label(__('psmonitor::filament/customer/pages/stock.table.columns.barcode'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.Item_Name')
                    ->label(__('psmonitor::filament/customer/pages/stock.table.columns.item_name'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('Quantity')
                    ->label(__('psmonitor::filament/customer/pages/stock.table.columns.quantity'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->badge()
                    ->alignCenter()
                    ->color(function ($record) {
                        $minAlert = $record->item->Min_Stock_Alert ?? 0;

                        return $record->Quantity <= $minAlert ? 'danger' : 'success';
                    })
                    ->description(function ($record) {
                        $minAlert = $record->item->Min_Stock_Alert ?? 0;

                        return __('psmonitor::filament/customer/pages/stock.table.columns.min_alert') . ': ' . $minAlert;
                    }),
            ])
            ->filters([
                Filter::make('low_stock')
                    ->label(__('psmonitor::filament/customer/pages/stock.table.filters.low_stock'))
                    ->toggle()
                    ->query(function (Builder $query): Builder {
                        return $query->whereHas('item', function (Builder $q) {
                            $q->whereColumn('stock.Quantity', '<=', 'item_master.Min_Stock_Alert');
                        });
                    }),

                Filter::make('quantity_range')
                    ->label(__('psmonitor::filament/customer/pages/stock.table.filters.quantity_range'))
                    ->form([
                        Select::make('range')
                            ->label(__('psmonitor::filament/customer/pages/stock.table.filters.quantity_range'))
                            ->options([
                                'zero'     => __('psmonitor::filament/customer/pages/stock.table.filters.ranges.zero'),
                                'positive' => __('psmonitor::filament/customer/pages/stock.table.filters.ranges.positive'),
                                'negative' => __('psmonitor::filament/customer/pages/stock.table.filters.ranges.negative'),
                            ]),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(($data['range'] ?? null) === 'zero', fn (Builder $q): Builder => $q->where('Quantity', 0))
                            ->when(($data['range'] ?? null) === 'positive', fn (Builder $q): Builder => $q->where('Quantity', '>', 0))
                            ->when(($data['range'] ?? null) === 'negative', fn (Builder $q): Builder => $q->where('Quantity', '<', 0));
                    }),
            ]);
    }
}
