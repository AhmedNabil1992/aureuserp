<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\PlayWait as PlayWaitModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class PlayWait extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.play-wait';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-play-circle';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/play-wait.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/play-wait.title');
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
        return __('psmonitor::filament/customer/pages/play-wait.table.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/play-wait.table.empty_state.description');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = PlayWaitModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = PlayWaitModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('Order_No')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.order_no'))
                    ->sortable(),

                TextColumn::make('Device_Name')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.device_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Start_Time')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.start_time'))
                    ->dateTime('Y-m-d H:i:s')
                    ->sortable(),

                TextColumn::make('End_Time')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.end_time'))
                    ->dateTime('Y-m-d H:i:s')
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('Period')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.period'))
                    ->suffix(' دقيقة')
                    ->sortable(),

                TextColumn::make('Cost')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.cost'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/play-wait.table.summaries.total_cost'))),

                TextColumn::make('Play_Type')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.play_type'))
                    ->placeholder('-'),

                TextColumn::make('Play_Price')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.play_price'))
                    ->numeric(decimalPlaces: 2),

                TextColumn::make('User_Name')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.user_name'))
                    ->placeholder('-'),

                TextColumn::make('Shift')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.columns.shift'))
                    ->placeholder('-'),
            ])
            ->actions([
                Action::make('device_current')
                    ->label(__('psmonitor::filament/customer/pages/play-wait.table.actions.device_current'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (PlayWaitModel $record): string => DeviceCurrent::getUrl() . '?order_no=' . urlencode((string) $record->Order_No)),
            ]);
    }
}
