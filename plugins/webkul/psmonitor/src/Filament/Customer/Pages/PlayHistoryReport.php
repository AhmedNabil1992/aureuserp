<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Count;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\PlayHistory;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class PlayHistoryReport extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.play-history-report';

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/play-history-report.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/play-history-report.title');
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

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = PlayHistory::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = PlayHistory::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->columns([
                TextColumn::make('TRX_Date')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.trx_date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('Invoice_No')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.invoice_no'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Device_Name')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.device_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Play_Type')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.play_type'))
                    ->searchable()
                    ->badge(),

                TextColumn::make('Hour_Price')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.hour_price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Play_Time')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.play_time'))
                    ->numeric()
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/play-history-report.table.summaries.total_minutes'))),

                TextColumn::make('Cost')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize([
                        Sum::make()->label(__('psmonitor::filament/customer/pages/play-history-report.table.summaries.total_cost')),
                        Count::make()->label(__('psmonitor::filament/customer/pages/play-history-report.table.summaries.count')),
                    ]),

                TextColumn::make('Start_Time')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.start_time'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('End_Time')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.end_time'))
                    ->dateTime()
                    ->sortable(),

                TextColumn::make('Username')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.username'))
                    ->searchable(),

                TextColumn::make('Shift_No')
                    ->label(__('psmonitor::filament/customer/pages/play-history-report.table.columns.shift_no'))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/play-history-report.table.filters.from')),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/play-history-report.table.filters.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('TRX_Date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('TRX_Date', '<=', $date),
                            );
                    }),
            ]);
    }
}
