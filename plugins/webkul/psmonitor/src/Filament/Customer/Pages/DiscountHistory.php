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
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
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

    public function getTableEmptyStateHeading(): ?string
    {
        return __('psmonitor::filament/customer/pages/discount-history.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/discount-history.empty_state.description');
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
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('Invoice_No')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.invoice_no'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Amount')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(
                        Sum::make()->label(__('psmonitor::filament/customer/pages/discount-history.table.summaries.total_amount'))
                    ),

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
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('Time')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.time'))
                    ->time('h:i A')
                    ->alignCenter(),

                TextColumn::make('Shift_No')
                    ->label(__('psmonitor::filament/customer/pages/discount-history.table.columns.shift_no'))
                    ->searchable()
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/discount-history.table.filters.from'))
                            ->default(now()->startOfMonth()),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/discount-history.table.filters.until'))
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('Date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('Date', '<=', $date),
                            );
                    }),
            ]);
    }
}
