<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
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
use Webkul\Psmonitor\Models\OrderWait as OrderWaitModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class OrderWait extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.order-wait';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-shopping-cart';

    protected static ?int $navigationSort = 6;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/order-wait.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/order-wait.title');
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
        return __('psmonitor::filament/customer/pages/order-wait.table.empty_state.heading');
    }

    public function getTableEmptyStateDescription(): ?string
    {
        return __('psmonitor::filament/customer/pages/order-wait.table.empty_state.description');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = OrderWaitModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = OrderWaitModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('Order_No')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.order_no'))
                    ->sortable(),

                TextColumn::make('Device_Name')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.device_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('item.Item_Name')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.item_name'))
                    ->sortable(),

                TextColumn::make('Quantity')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.quantity'))
                    ->sortable(),

                TextColumn::make('Price')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Amount')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.amount'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize(Sum::make()->label(__('psmonitor::filament/customer/pages/order-wait.table.summaries.total_amount'))),

                IconColumn::make('Print')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.print'))
                    ->boolean(),

                TextColumn::make('Order_By')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.order_by'))
                    ->placeholder('-'),

                TextColumn::make('Notes')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.columns.notes'))
                    ->limit(40)
                    ->tooltip(fn ($record) => $record->Notes),
            ])
            ->actions([
                Action::make('device_current')
                    ->label(__('psmonitor::filament/customer/pages/order-wait.table.actions.device_current'))
                    ->icon('heroicon-o-eye')
                    ->color('info')
                    ->url(fn (OrderWaitModel $record): string => DeviceCurrent::getUrl() . '?order_no=' . urlencode((string) $record->Order_No)),
            ]);
    }
}
