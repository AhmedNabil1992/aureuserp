<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
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
use Illuminate\Support\Facades\Storage;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Psmonitor\Filament\Customer\Actions\ExportToExcelAction;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasPsLicenseAccess;
use Webkul\Psmonitor\Filament\Customer\Concerns\HasRemoteTablePaginationForPage;
use Webkul\Psmonitor\Filament\Customer\Widgets\LicenseSelectorWidget;
use Webkul\Psmonitor\Models\DeviceType as DeviceTypeModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class DeviceTypes extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.device-types';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-squares-2x2';

    protected static ?int $navigationSort = 1;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/device-types.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/device-types.title');
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
        return __('psmonitor::filament/customer/pages/device-types.table.empty_state.heading');
    }

    public function table(Table $table): Table
    {
        $customer = Auth::guard('customer')->user();
        $query = null;

        if ($customer instanceof Partner) {
            try {
                $license = app(CustomerLicenseResolver::class)->resolveRemoteLicense($customer);

                if ($license && RemoteModel::canConnectToHost($license->server_ip)) {
                    $query = DeviceTypeModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = DeviceTypeModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'asc')
            ->headerActions([
                ExportToExcelAction::make(),
            ])
            ->columns([
                TextColumn::make('ID')
                    ->label(__('psmonitor::filament/customer/pages/device-types.table.columns.id'))
                    ->sortable(),

                TextColumn::make('Name')
                    ->label(__('psmonitor::filament/customer/pages/device-types.table.columns.name'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Description')
                    ->label(__('psmonitor::filament/customer/pages/device-types.table.columns.description'))
                    ->searchable()
                    ->wrap(),

                ToggleColumn::make('IsActive')
                    ->label(__('psmonitor::filament/customer/pages/device-types.table.columns.is_active'))
                    ->onColor('success')
                    ->offColor('danger'),
            ])
            ->filters([
                Filter::make('active_only')
                    ->label(__('psmonitor::filament/customer/pages/device-types.table.filters.active_only'))
                    ->query(fn (Builder $query): Builder => $query->where('IsActive', 1)),
            ])
            ->actions([
                Action::make('add_play_type')
                    ->label(__('psmonitor::filament/customer/pages/device-types.table.actions.add_play_type'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading(__('psmonitor::filament/customer/pages/device-types.table.actions.add_play_type'))
                    ->modalWidth('sm')
                    ->form([
                        TextInput::make('type_name')
                            ->label('نوع اللعب')
                            ->required()
                            ->maxLength(255),

                        FileUpload::make('image')
                            ->label('صورة نوع اللعب')
                            ->image()
                            ->required()
                            ->disk('public')
                            ->directory('temp_play_types'),
                    ])
                    ->action(function (DeviceTypeModel $record, array $data) {
                        try {
                            $imagePath = $data['image'];
                            $imageBinary = Storage::disk('public')->get($imagePath);
                            $hexImage = bin2hex($imageBinary);

                            $query = "
                                DECLARE @OutResult VARCHAR(255);
                                EXEC New_Play_Type
                                    @I_Device_ID = ?,
                                    @I_Type_Name = ?,
                                    @I_Image = 0x{$hexImage},
                                    @O_Result = @OutResult OUTPUT;
                                SELECT @OutResult AS ResultMessage;
                            ";

                            $connectionName = $record->getConnectionName();
                            $result = DB::connection($connectionName)->selectOne($query, [
                                $record->ID,
                                $data['type_name'],
                            ]);

                            Storage::disk('public')->delete($imagePath);

                            Notification::make()
                                ->title('نجاح')
                                ->body($result->ResultMessage ?? 'تمت إضافة نوع اللعب بنجاح.')
                                ->success()
                                ->send();

                        } catch (\Throwable $e) {
                            Notification::make()
                                ->title('خطأ في التنفيذ')
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }
}
