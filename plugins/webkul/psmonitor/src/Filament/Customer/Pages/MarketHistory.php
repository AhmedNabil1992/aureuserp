<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
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
use Webkul\Psmonitor\Models\MarketHistory as MarketHistoryModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class MarketHistory extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.market-history';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    protected static ?int $navigationSort = 9;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/market-history.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/market-history.title');
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
        return __('psmonitor::filament/customer/pages/market-history.table.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/market-history.table.empty_state.description');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = MarketHistoryModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = MarketHistoryModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('TRX_Date')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.trx_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('TRX_Time')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.trx_time'))
                    ->time('h:i A')
                    ->icon('heroicon-o-clock')
                    ->iconPosition('before')
                    ->iconColor('primary')
                    ->alignCenter(),

                TextColumn::make('Invoice_No')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.invoice_no'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Client_Name')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.client_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.Item_Name')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.item_name'))
                    ->placeholder('-')
                    ->searchable(),

                TextColumn::make('Quantity')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.quantity'))
                    ->sortable(),

                TextColumn::make('Price')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.price'))
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('Amount')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/market-history.table.summaries.total_amount'))),

                TextColumn::make('Username')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.username'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('Shift')
                    ->label(__('psmonitor::filament/customer/pages/market-history.table.columns.shift'))
                    ->sortable(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/market-history.table.filters.from'))
                            ->default(now()->startOfMonth()->toDateString()),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/market-history.table.filters.until'))
                            ->default(now()->toDateString()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['from'] ?? null), fn (Builder $q): Builder => $q->whereDate('TRX_Date', '>=', $data['from']))
                            ->when(filled($data['until'] ?? null), fn (Builder $q): Builder => $q->whereDate('TRX_Date', '<=', $data['until']));
                    }),

                Filter::make('search_fields')
                    ->form([
                        TextInput::make('invoice_no')
                            ->label(__('psmonitor::filament/customer/pages/market-history.table.filters.invoice_no')),
                        TextInput::make('client_name')
                            ->label(__('psmonitor::filament/customer/pages/market-history.table.filters.client_name')),
                        TextInput::make('username')
                            ->label(__('psmonitor::filament/customer/pages/market-history.table.filters.username')),
                        TextInput::make('item_id')
                            ->label(__('psmonitor::filament/customer/pages/market-history.table.filters.item_id')),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(filled($data['invoice_no'] ?? null), fn (Builder $q): Builder => $q->where('Invoice_No', $data['invoice_no']))
                            ->when(filled($data['client_name'] ?? null), fn (Builder $q): Builder => $q->where('Client_Name', 'like', '%' . $data['client_name'] . '%'))
                            ->when(filled($data['username'] ?? null), fn (Builder $q): Builder => $q->where('Username', 'like', '%' . $data['username'] . '%'))
                            ->when(filled($data['item_id'] ?? null), fn (Builder $q): Builder => $q->where('Item_ID', $data['item_id']));
                    }),
            ]);
    }
}
