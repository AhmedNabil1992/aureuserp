<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\Invoices;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class SalesReport extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.sales-report';

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/sales-report.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/sales-report.title');
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
                    $query = Invoices::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = Invoices::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->columns([
                TextColumn::make('Date')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('Invoice_No')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.invoice_no'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Amount')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/sales-report.table.summaries.total_amount'))),

                TextColumn::make('Discount')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.discount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/sales-report.table.summaries.total_discount'))),

                TextColumn::make('Services')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.services'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/sales-report.table.summaries.total_services'))),

                TextColumn::make('Tax')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.tax'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Total')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.total'))
                    ->numeric(decimalPlaces: 2)
                    ->weight('bold')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/sales-report.table.summaries.grand_total'))),

                TextColumn::make('Username')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.username'))
                    ->searchable(),

                TextColumn::make('Shift_No')
                    ->label(__('psmonitor::filament/customer/pages/sales-report.table.columns.shift_no'))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/sales-report.table.filters.from')),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/sales-report.table.filters.until')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('Date', '>=', $date),
                            )
                            ->when(
                                $data['until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('Date', '<=', $date),
                            );
                    }),
            ]);
    }
}
