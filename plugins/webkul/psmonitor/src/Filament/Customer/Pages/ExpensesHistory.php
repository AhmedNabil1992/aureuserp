<?php

namespace Webkul\Psmonitor\Filament\Customer\Pages;

use Filament\Actions\Action;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Tables;
use Filament\Tables\Columns\Summarizers\Sum;
use Filament\Tables\Columns\TextColumn;
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
use Webkul\Psmonitor\Models\ExpensesHistory as ExpensesHistoryModel;
use Webkul\Psmonitor\Models\RemoteModel;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

class ExpensesHistory extends Page implements HasTable
{
    use HasPsLicenseAccess, HasRemoteTablePaginationForPage;

    public bool $connectionFailed = false;

    protected string $view = 'psmonitor::filament.customer.pages.expenses-history';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-banknotes';

    protected static ?int $navigationSort = 11;

    public static function getNavigationLabel(): string
    {
        return __('psmonitor::filament/customer/pages/expenses-history.title');
    }

    public function getTitle(): string
    {
        return __('psmonitor::filament/customer/pages/expenses-history.title');
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
        return __('psmonitor::filament/customer/pages/expenses-history.table.empty_state.heading');
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
                    $query = ExpensesHistoryModel::forLicense($license);
                } else {
                    $this->connectionFailed = true;
                }
            } catch (Throwable $e) {
                $this->connectionFailed = true;
            }
        }

        if ($this->connectionFailed || ! $query) {
            $this->connectionFailed = true;

            $query = ExpensesHistoryModel::emptyQuery();
        }

        return static::applyRemoteTablePagination($table)
            ->query($query)
            ->defaultSort('ID', 'desc')
            ->headerActions([
                ExportToExcelAction::make(),
                Action::make('add_expense')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.actions.add_expense'))
                    ->icon('heroicon-o-plus-circle')
                    ->color('success')
                    ->modalHeading('إضافة مصروف جديد')
                    ->form([
                        Select::make('Expenses_Type')
                            ->label('نوع المصروف')
                            ->required()
                            ->searchable()
                            ->options(function (): array {
                                try {
                                    $connName = static::resolveConnectionName();
                                    if (! $connName) return [];
                                    return DB::connection($connName)
                                        ->table('expenses_type')
                                        ->pluck('Expenses_Type', 'Expenses_Type')
                                        ->toArray();
                                } catch (\Throwable) {
                                    return [];
                                }
                            }),

                        Textarea::make('Expenses_Remark')
                            ->label('البيان')
                            ->maxLength(255)
                            ->rows(2),

                        TextInput::make('Amount')
                            ->label('المبلغ')
                            ->required()
                            ->numeric()
                            ->minValue(0.01),

                        Select::make('Username')
                            ->label('المستخدم')
                            ->required()
                            ->searchable()
                            ->options(function (): array {
                                try {
                                    $connName = static::resolveConnectionName();
                                    if (! $connName) return [];
                                    return DB::connection($connName)
                                        ->table('users')
                                        ->where('IsActive', 1)
                                        ->where('Username', '!=', 'System Admin')
                                        ->pluck('Username', 'Username')
                                        ->toArray();
                                } catch (\Throwable) {
                                    return [];
                                }
                            }),
                    ])
                    ->action(function (array $data): void {
                        $connName = static::resolveConnectionName();
                        if (! $connName) return;

                        DB::connection($connName)->statement(
                            "EXEC Insert_Expenses_Record @I_Expenses_Type = ?, @I_Expenses_Remark = ?, @I_Amount = ?, @I_Username = ?",
                            [
                                $data['Expenses_Type'],
                                $data['Expenses_Remark'] ?? '',
                                $data['Amount'],
                                $data['Username'],
                            ]
                        );

                        Notification::make()
                            ->title('تم إضافة المصروف بنجاح')
                            ->success()
                            ->send();
                    }),
            ])
            ->columns([
                TextColumn::make('ID')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.id'))
                    ->sortable(),

                TextColumn::make('Expenses_Type')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.expenses_type'))
                    ->searchable()
                    ->sortable(),

                TextColumn::make('Expenses_Remark')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.expenses_remark'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('Expenses_AMT')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.expenses_amt'))
                    ->numeric(decimalPlaces: 2)
                    ->sortable()
                    ->summarize([
                        Sum::make()->label(__('psmonitor::filament/customer/pages/expenses-history.table.summaries.total'))->numeric(2),
                    ]),

                TextColumn::make('Username')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.username'))
                    ->searchable()
                    ->placeholder('-'),

                TextColumn::make('Shift')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.shift'))
                    ->placeholder('-'),

                TextColumn::make('TRX_Date')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.trx_date'))
                    ->date('Y-m-d')
                    ->sortable(),

                TextColumn::make('TRX_Time')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.columns.trx_time'))
                    ->time('h:i A')
                    ->icon('heroicon-o-clock')
                    ->iconPosition('before')
                    ->iconColor('primary')
                    ->alignCenter(),
            ])
            ->filters([
                Filter::make('date_range')
                    ->form([
                        DatePicker::make('from')
                            ->label(__('psmonitor::filament/customer/pages/expenses-history.table.filters.from'))
                            ->default(now()->startOfMonth()->toDateString()),
                        DatePicker::make('until')
                            ->label(__('psmonitor::filament/customer/pages/expenses-history.table.filters.until'))
                            ->default(now()->toDateString()),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                filled($data['from'] ?? null),
                                fn (Builder $q) => $q->whereDate('TRX_Date', '>=', $data['from'])
                            )
                            ->when(
                                filled($data['until'] ?? null),
                                fn (Builder $q) => $q->whereDate('TRX_Date', '<=', $data['until'])
                            );
                    }),
            ])
            ->actions([
                Action::make('delete_expense')
                    ->label(__('psmonitor::filament/customer/pages/expenses-history.table.actions.delete_expense'))
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->modalHeading('تأكيد الحذف')
                    ->modalDescription('هل أنت متأكد من حذف هذا السجل؟ لا يمكن التراجع عن هذه العملية.')
                    ->action(function (ExpensesHistoryModel $record): void {
                        $username = Auth::guard('customer')->user()->name ?? 'Admin';
                        $connName = static::resolveConnectionName();
                        if (! $connName) return;

                        $rows = DB::connection($connName)->select(
                            "DECLARE @O_Result VARCHAR(255);
                             EXEC Delete_Expenses_History @I_ID = ?, @I_Username = ?, @O_Result = @O_Result OUTPUT;
                             SELECT @O_Result AS result;",
                            [$record->ID, $username]
                        );

                        $result = $rows[0]->result ?? null;

                        if ($result && strtoupper(trim($result)) !== 'OK') {
                            Notification::make()
                                ->title('فشل الحذف')
                                ->body($result)
                                ->danger()
                                ->send();
                        } else {
                            Notification::make()
                                ->title('تم حذف المصروف بنجاح')
                                ->success()
                                ->send();
                        }
                    }),
            ]);
    }
}
