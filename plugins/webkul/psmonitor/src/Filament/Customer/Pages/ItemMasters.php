<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\Categories;
use Webkul\Psmonitor\Models\ItemMaster as ItemMasterModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class ItemMasters extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.item-masters';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box';

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/item-masters.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/item-masters.title');
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
        return __('psmonitor::filament/customer/pages/item-masters.table.empty_state.heading');
    }

    protected static function resolveConnectionName(): ?string
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

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = ItemMasterModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = ItemMasterModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
                Action::make('add_item')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.actions.add_item'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading('إضافة صنف جديد')
                    ->form([
                        Select::make('I_Cate_ID')
                            ->label('المجموعة')
                            ->required()
                            ->searchable()
                            ->preload()
                            ->options(function (): array {
                                try {
                                    $connName = static::resolveConnectionName();
                                    if (! $connName) return [];
                                    return DB::connection($connName)
                                        ->table('categories')
                                        ->orderBy('Cate_Name')
                                        ->pluck('Cate_Name', 'ID')
                                        ->mapWithKeys(fn ($name, $id) => [(string) $id => (string) $name])
                                        ->toArray();
                                } catch (\Throwable) {
                                    return [];
                                }
                            }),

                        TextInput::make('I_Product_Name')
                            ->label('اسم الصنف')
                            ->required()
                            ->maxLength(255),

                        TextInput::make('I_Product_Price')
                            ->label('سعر الصنف')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('I_Table_Price')
                            ->label('سعر الطاولة')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        TextInput::make('I_Direct_Price')
                            ->label('السعر المباشر')
                            ->required()
                            ->numeric()
                            ->minValue(0),

                        Toggle::make('I_Sales')
                            ->label('مبيعات')
                            ->default(false)
                            ->dehydrateStateUsing(fn (bool $state): string => $state ? 'Checked' : 'UnChecked')
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        $connName = static::resolveConnectionName();
                        if (! $connName) return;

                        try {
                            $rows = DB::connection($connName)->select(
                                'SET NOCOUNT ON; '
                                . 'DECLARE @O_Result NVARCHAR(MAX), @O_Product_ID INT, @I_Item_Image VARBINARY(MAX); '
                                . 'SET @I_Item_Image = NULL; '
                                . 'EXEC Insert_New_Product '
                                . '@I_Cate_ID = ?, '
                                . '@I_Product_Name = ?, '
                                . '@I_Product_Price = ?, '
                                . '@I_Product = ?, '
                                . '@I_Sales = ?, '
                                . '@O_Result = @O_Result OUTPUT, '
                                . '@O_Product_ID = @O_Product_ID OUTPUT, '
                                . '@I_Table_Price = ?, '
                                . '@I_Direct_Price = ?, '
                                . '@I_Item_Image = @I_Item_Image; '
                                . 'SELECT @O_Result AS result, @O_Product_ID AS product_id;',
                                [
                                    (int) $data['I_Cate_ID'],
                                    (string) $data['I_Product_Name'],
                                    (float) $data['I_Product_Price'],
                                    'UnChecked',
                                    (string) $data['I_Sales'],
                                    (float) $data['I_Table_Price'],
                                    (float) $data['I_Direct_Price'],
                                ]
                            );

                            $result = (string) ($rows[0]->result ?? 'تمت العملية بنجاح');
                            $isDuplicate = str_contains($result, 'موجود بالفعل');

                            Notification::make()
                                ->title($result)
                                ->color($isDuplicate ? 'danger' : 'success')
                                ->send();
                        } catch (QueryException $exception) {
                            $rawMessage = $exception->getMessage();
                            $cleanMessage = trim((string) preg_replace('/\s*\(Connection:.*$/', '', $rawMessage));

                            Notification::make()
                                ->title('خطأ في قاعدة البيانات')
                                ->body($cleanMessage)
                                ->danger()
                                ->send();
                        }
                    }),
            ])
            ->columns([
                TextColumn::make('group.Cate_Name')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.group'))
                    ->sortable(),

                TextColumn::make('Code')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.code'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('Item_Name')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.item_name'))
                    ->sortable()
                    ->searchable(),

                TextColumn::make('Item_Price')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.item_price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Table_Price')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.table_price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Direct_Price')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.direct_price'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable(),

                TextColumn::make('Min_Stock_Alert')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.min_stock_alert'))
                    ->sortable(),

                IconColumn::make('IsProduct')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.is_product'))
                    ->boolean(),

                IconColumn::make('IsSales')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.is_sales'))
                    ->boolean(),

                ToggleColumn::make('IsActive')
                    ->label(__('psmonitor::filament/customer/pages/item-masters.table.columns.is_active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ]);
    }
}
