<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\Device as DeviceModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class Devices extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.devices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-cpu-chip';

    protected static ?int $navigationSort = 2;

    protected static ?array $deviceTypesMap = null;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/devices.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/devices.title');
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
        return __('psmonitor::filament/customer/pages/devices.table.empty_state.heading');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = DeviceModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = DeviceModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'asc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('Device_Name')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.device_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Device_Type')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.device_type'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => static::resolveDeviceTypeName($state))
                    ->sortable(),

                TextColumn::make('Kind')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.kind'))
                    ->placeholder('-')
                    ->sortable(),

                TextColumn::make('IP_Address')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.ip_address'))
                    ->searchable()
                    ->copyable(),

                TextColumn::make('Status')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.status'))
                    ->badge()
                    ->color(fn (?string $state): string => ($state === 'متاح') ? 'success' : 'danger')
                    ->sortable(),

                TextColumn::make('Limit_Time')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.limit_time'))
                    ->suffix(' دقيقة')
                    ->sortable(),

                ToggleColumn::make('IsActive')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.columns.is_active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                Filter::make('is_active')
                    ->label(__('psmonitor::filament/customer/pages/devices.table.filters.is_active'))
                    ->query(fn (Builder $query): Builder => $query->where('IsActive', 1)),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('psmonitor::filament/customer/pages/devices.table.actions.edit'))
                    ->modalHeading('تعديل الجهاز')
                    ->form([
                        TextInput::make('Device_Name')->label('اسم الجهاز')->disabled(),
                        TextInput::make('Limit_Time')
                            ->label('الحد الأدنى')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                    ])
                    ->using(function (DeviceModel $record, array $data): DeviceModel {
                        $record->update([
                            'Limit_Time' => (int) $data['Limit_Time'],
                        ]);

                        return $record;
                    }),
            ]);
    }

    protected static function resolveDeviceTypeName($deviceTypeId): string
    {
        $map = static::resolveDeviceTypesMap();

        return $map[(int) $deviceTypeId] ?? (string) $deviceTypeId;
    }

    protected static function resolveDeviceTypesMap(): array
    {
        if (is_array(static::$deviceTypesMap)) {
            return static::$deviceTypesMap;
        }

        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            static::$deviceTypesMap = [];
            return static::$deviceTypesMap;
        }

        $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);
        if (! $license) {
            static::$deviceTypesMap = [];
            return static::$deviceTypesMap;
        }

        $connectionName = RemoteModel::getRemoteConnectionName($license);
        if (! $connectionName) {
            static::$deviceTypesMap = [];
            return static::$deviceTypesMap;
        }

        static::$deviceTypesMap = DB::connection($connectionName)
            ->table('Device_Type')
            ->select('ID', 'Name')
            ->pluck('Name', 'ID')
            ->mapWithKeys(fn ($name, $id) => [(int) $id => (string) $name])
            ->toArray();

        return static::$deviceTypesMap;
    }
}
