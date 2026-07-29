<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Carbon\WeekDay;
use Filament\Actions\Action;
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
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Models\Shifts;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class ShiftsAuditReport extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.shifts-audit-report';

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/shifts-audit-report.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/shifts-audit-report.title');
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
                    $query = Shifts::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = Shifts::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('Shift_No', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('Shift_No')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.shift_no'))
                    ->sortable()
                    ->weight('bold'),
                TextColumn::make('Shift_Date')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.shift_date'))
                    ->date('Y-m-d')
                    ->sortable(),
                TextColumn::make('Shift_From')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.shift_from'))
                    ->time('h:i A')
                    ->alignCenter(),
                TextColumn::make('Shift_To')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.shift_to'))
                    ->time('h:i A')
                    ->alignCenter(),
                TextColumn::make('Shift_Open')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.shift_open'))
                    ->searchable(),
                TextColumn::make('Shift_Close')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.shift_close'))
                    ->placeholder('-')
                    ->searchable(),
                TextColumn::make('Start_AMT')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.start_amt'))
                    ->numeric(decimalPlaces: 2),
                TextColumn::make('Playstation')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.playstation'))
                    ->numeric(decimalPlaces: 2)
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.summaries.total_playstation'))),
                TextColumn::make('Sales_AMT')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.sales_amt'))
                    ->numeric(decimalPlaces: 2)
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.summaries.total_sales'))),
                TextColumn::make('Expenses_AMT')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.expenses_amt'))
                    ->numeric(decimalPlaces: 2)
                    ->color('danger')
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.summaries.total_expenses'))),
                TextColumn::make('Remain_AMT')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.remain_amt'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('Actual_Amt')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.actual_amt'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),
                TextColumn::make('Different')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.different'))
                    ->numeric(decimalPlaces: 2)
                    ->badge()
                    ->color(fn ($state) => match (true) {
                        (float) $state < 0 => 'danger',
                        (float) $state > 0 => 'success',
                        default => 'gray',
                    }),
                TextColumn::make('Status')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.columns.status'))
                    ->badge()
                    ->color(fn ($state) => match ($state) {
                        'Open' => 'warning',
                        'Close' => 'success',
                        default => 'gray',
                    }),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.filters.from'))
                            ->default(now()->startOfWeek(WeekDay::Saturday)),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.filters.until'))
                            ->default(now()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('Shift_Date', '>=', $date),
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('Shift_Date', '<=', $date),
                            );
                    }),
            ])
            ->actions([
                Action::make('details')
                    ->label(__('psmonitor::filament/customer/pages/shifts-audit-report.table.actions.details'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->modalHeading(__('psmonitor::filament/customer/pages/shifts-audit-report.table.actions.details'))
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false)
                    ->modalSubmitActionLabel(__('psmonitor::filament/customer/pages/shifts-audit-report.table.actions.details'))
                    ->modalContent(fn (Shifts $record): \Illuminate\Contracts\View\View => view('psmonitor::filament.customer.pages.partials.ps-shift-details', ['shift' => $record])),
            ]);
    }
}
