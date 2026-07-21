<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Pages\Page;
use Filament\Tables;
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
use Webkul\Psmonitor\Models\Discount;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class DiscountHistory extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.discount-history';

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/discount-history.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/discount-history.title');
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
                    $query = Discount::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = Discount::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->columns([
                TextColumn::make('Invoice_No')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.invoice_no'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Amount')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Reason')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.reason'))
                    ->searchable()
                    ->wrap(),

                TextColumn::make('Username')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.username'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Date')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.date'))
                    ->date()
                    ->sortable(),

                TextColumn::make('Time')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.time')),

                TextColumn::make('Shift_No')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.shift_no'))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/discount-history.table.filters.from')),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/discount-history.table.filters.until')),
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
