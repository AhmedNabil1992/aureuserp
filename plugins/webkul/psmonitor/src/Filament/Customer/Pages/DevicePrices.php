<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Get;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Grouping\Group;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\DevicePrice as DevicePriceModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class DevicePrices extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.device-prices';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-currency-dollar';

    protected static ?int $navigationSort = 3;

    protected static ?array $deviceTypesMap = null;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/device-prices.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/device-prices.title');
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
        return __('psmonitor::filament/customer/pages/device-prices.table.empty_state.heading');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = DevicePriceModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = DevicePriceModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'asc')
            ->groups([
                Group::make('Device_Name')
                    ->label('اسم الجهاز'),
            ])
            ->defaultGroup('Device_Name')
            ->headerActions([
                ExportToExcelAction::make(),
                Action::make('add_new_price')
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.actions.add_new_price'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('primary')
                    ->modalHeading('إضافة سعر جهاز جديد')
                    ->form([
                        Select::make('Device_Type')
                            ->label('نوع الجهاز')
                            ->options(function () {
                                $conn = static::getCustomerDbConnectionName();
                                if (!$conn) return [];
                                return DB::connection($conn)->table('Device_Type')
                                    ->where('IsActive', 1)
                                    ->pluck('Name', 'ID');
                            })
                            ->live()
                            ->required(),

                        Select::make('Device_Name')
                            ->label('اسم الجهاز')
                            ->multiple()
                            ->options(function (Get $get) {
                                $typeId = $get('Device_Type');
                                $conn = static::getCustomerDbConnectionName();
                                if (!$typeId || !$conn) return [];
                                return DB::connection($conn)->table('devices')
                                    ->where('Kind', 'Time')
                                    ->where('Device_Type', $typeId)
                                    ->pluck('Device_Name', 'Device_Name');
                            })
                            ->required(),

                        Select::make('Game_Type')
                            ->label('نوع اللعب')
                            ->options(function (Get $get) {
                                $typeId = $get('Device_Type');
                                $conn = static::getCustomerDbConnectionName();
                                if (!$typeId || !$conn) return [];
                                return DB::connection($conn)->table('stat_img')
                                    ->where('Dev_Type', $typeId)
                                    ->where('Dev_Stat', '!=', 'مغلق')
                                    ->pluck('Dev_Stat', 'Dev_Stat');
                            })
                            ->required(),

                        TextInput::make('Hour_Price')
                            ->label('سعر الساعة')
                            ->numeric()
                            ->minValue(0)
                            ->required(),

                        TimePicker::make('S_From')
                            ->label('يبدأ من')
                            ->seconds(false)
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        $conn = static::getCustomerDbConnectionName();
                        if (!$conn) return;

                        try {
                            DB::connection($conn)->beginTransaction();

                            foreach ($data['Device_Name'] as $deviceName) {
                                DB::connection($conn)->statement(
                                    "EXEC Insert_New_Device_Price @I_Device_Name = ?, @I_Game_Type = ?, @I_From = ?, @I_Price = ?",
                                    [
                                        $deviceName,
                                        $data['Game_Type'],
                                        $data['S_From'],
                                        $data['Hour_Price']
                                    ]
                                );
                            }

                            DB::connection($conn)->commit();

                            Notification::make()
                                ->title('نجاح')
                                ->body('تمت إضافة الأسعار بنجاح.')
                                ->success()
                                ->send();

                        } catch (\Throwable $e) {
                            DB::connection($conn)->rollBack();
                            Notification::make()
                                ->title('خطأ في التنفيذ')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('Device_Type')
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.columns.device_type'))
                    ->formatStateUsing(fn ($state) => static::resolveDeviceTypeName($state))
                    ->badge()
                    ->sortable(),

                TextColumn::make('Device_Name')
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.columns.device_name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Game_Type')
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.columns.game_type'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Hour_Price')
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.columns.hour_price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('S_From')
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.columns.s_from'))
                    ->time('H:i')
                    ->sortable(),
            ])
            ->actions([
                EditAction::make()
                    ->label(__('psmonitor::filament/customer/pages/device-prices.table.actions.edit_price'))
                    ->modalHeading('تعديل سعر الجهاز')
                    ->form([
                        TextInput::make('Device_Name')->label('اسم الجهاز')->disabled(),
                        TextInput::make('Game_Type')->label('نوع اللعب')->disabled(),
                        TextInput::make('Hour_Price')
                            ->label('سعر الساعة')
                            ->numeric()
                            ->minValue(0)
                            ->required(),
                        TimePicker::make('S_From')
                            ->label('يبدأ من')
                            ->seconds(false)
                            ->required(),
                    ])
                    ->using(function (DevicePriceModel $record, array $data): DevicePriceModel {
                        $record->update([
                            'Hour_Price' => $data['Hour_Price'],
                            'S_From' => $data['S_From'],
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

    protected static function getCustomerDbConnectionName(): ?string
    {
        $customer = Auth::guard('customer')->user();
        if (! $customer) {
            return null;
        }

        $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);
        if (! $license) {
            return null;
        }

        return RemoteModel::getRemoteConnectionName($license);
    }

    protected static function resolveDeviceTypesMap(): array
    {
        if (is_array(static::$deviceTypesMap)) {
            return static::$deviceTypesMap;
        }

        $conn = static::getCustomerDbConnectionName();
        if (! $conn) {
            static::$deviceTypesMap = [];
            return static::$deviceTypesMap;
        }

        static::$deviceTypesMap = DB::connection($conn)
            ->table('Device_Type')
            ->select('ID', 'Name')
            ->pluck('Name', 'ID')
            ->mapWithKeys(fn ($name, $id) => [(int) $id => (string) $name])
            ->toArray();

        return static::$deviceTypesMap;
    }
}
