<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
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
use Webkul\Psmonitor\Models\TRXHistory as TRXHistoryModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class TRXHistory extends Page implements Tables\Contracts\HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.trx-history';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrows-right-left';

    protected static ?int $navigationSort = 12;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/trx-history.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/trx-history.title');
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
        return __('psmonitor::filament/customer/pages/trx-history.table.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/trx-history.table.empty_state.description');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = TRXHistoryModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = TRXHistoryModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('TRX_Date')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.trx_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('TRX_Time')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.trx_time'))
                    ->time('h:i A')
                    ->icon('heroicon-o-clock')
                    ->iconPosition('before')
                    ->iconColor('primary')
                    ->alignCenter(),

                TextColumn::make('TRX_Type')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.trx_type'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('TRX_Name')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.trx_name'))
                    ->searchable()
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('Amount')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/trx-history.table.summaries.total_amount'))),

                TextColumn::make('Username')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.username'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('Shift')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.shift'))
                    ->sortable()
                    ->placeholder('-'),

                TextColumn::make('Reference')
                    ->label(__('psmonitor::filament/customer/pages/trx-history.table.columns.reference'))
                    ->searchable()
                    ->placeholder('-'),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/trx-history.table.filters.from'))
                            ->default(now()->startOfMonth()->toDateString()),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/trx-history.table.filters.until'))
                            ->default(now()->toDateString()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['from'] ?? null), fn (Builder $q): Builder => $q->whereDate('TRX_Date', '>=', $data['from']))
                            ->when(filled($data['until'] ?? null), fn (Builder $q): Builder => $q->whereDate('TRX_Date', '<=', $data['until']));
                    }),

                Filter::make('search_fields')
                    ->form([
                        TextInput::make('trx_type')
                            ->label(__('psmonitor::filament/customer/pages/trx-history.table.filters.trx_type')),
                        TextInput::make('trx_name')
                            ->label(__('psmonitor::filament/customer/pages/trx-history.table.filters.trx_name')),
                        TextInput::make('username')
                            ->label(__('psmonitor::filament/customer/pages/trx-history.table.filters.username')),
                        TextInput::make('reference')
                            ->label(__('psmonitor::filament/customer/pages/trx-history.table.filters.reference')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['trx_type'] ?? null), fn (Builder $q): Builder => $q->where('TRX_Type', 'like', '%' . $data['trx_type'] . '%'))
                            ->when(filled($data['trx_name'] ?? null), fn (Builder $q): Builder => $q->where('TRX_Name', 'like', '%' . $data['trx_name'] . '%'))
                            ->when(filled($data['username'] ?? null), fn (Builder $q): Builder => $q->where('Username', 'like', '%' . $data['username'] . '%'))
                            ->when(filled($data['reference'] ?? null), fn (Builder $q): Builder => $q->where('Reference', 'like', '%' . $data['reference'] . '%'));
                    }),
            ]);
    }
}
