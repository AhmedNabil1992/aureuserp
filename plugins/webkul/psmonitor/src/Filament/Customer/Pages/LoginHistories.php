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
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\LoginHistory;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class LoginHistories extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.login-histories';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?int $navigationSort = 18;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/login-history.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/login-history.title');
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
        return __('psmonitor::filament/customer/pages/login-history.table.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/login-history.table.empty_state.description');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = LoginHistory::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = LoginHistory::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('ID')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.id'))
                    ->sortable(),

                TextColumn::make('UserID')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.user_id'))
                    ->sortable(),

                TextColumn::make('UserName')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.user_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Date')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('Time')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.time'))
                    ->time('h:i A')
                    ->icon('heroicon-o-clock')
                    ->iconPosition('before')
                    ->iconColor('primary')
                    ->alignCenter(),

                TextColumn::make('IPAddress')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.ip_address'))
                    ->sortable(),

                TextColumn::make('Remark')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.remark'))
                    ->sortable()
                    ->alignCenter(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->label(__('psmonitor::filament/customer/pages/login-history.table.columns.date'))
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/login-history.table.filters.from'))
                            ->default(now()->startOfMonth()->toDateString()),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/login-history.table.filters.until'))
                            ->default(now()->toDateString()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('Date', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn (Builder $query, $date): Builder => $query->whereDate('Date', '<=', $date)
                            );
                    }),
            ]);
    }
}
