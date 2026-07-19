<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\TopUp;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\Profile;
use Webkul\Wifi\Models\PermanentUser;
use Filament\Forms\Components\Textarea;
use Webkul\Wifi\Services\PermanentUserService;
use Throwable;

class Topups extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'wifi::filament.customer.pages.topups';

    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/topup.title');
    }
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-user-plus';

    protected static ?int $navigationSort = 4;
    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if (! Schema::hasTable('wifi_partner_clouds')) {
            return false;
        }

        return WifiPartnerCloud::where('partner_id', $user->id)->exists();
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        $partnerId = $user?->id;

        $query = TopUp::query();

        if ($partnerId) {
            $cloudIds = WifiPartnerCloud::where('partner_id', $partnerId)->pluck('cloud_id')->toArray();
            $query->whereIn('cloud_id', $cloudIds);
        } else {
            $query->whereRaw('1=0');
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('cloud.name')->label(__('wifi::filament/customer/pages/topup.table.columns.cloud'))->searchable()->sortable(),    
                TextColumn::make('permanentUser.username')->label(__('wifi::filament/customer/pages/topup.table.columns.permanent_user')),
                TextColumn::make('data')->label(__('wifi::filament/customer/pages/topup.table.columns.data'))->searchable()->sortable()
                ->formatStateUsing(function ($state) {
                        if ($state === null || $state === '') {
                            return '-';
                        }

                        if (!is_numeric($state)) {
                            return (string) $state;
                        }

                        $bytes = (float) $state;

                        if ($bytes >= 1073741824) {
                            return number_format($bytes / 1073741824, 2) . ' GB';
                        }

                        return number_format($bytes / 1048576, 2) . ' MB';
                    }),
                TextColumn::make('time')->label(__('wifi::filament/customer/pages/topup.table.columns.time')),
                TextColumn::make('days_to_use')->label(__('wifi::filament/customer/pages/topup.table.columns.days_to_use')),
                TextColumn::make('comment')->label(__('wifi::filament/customer/pages/topup.table.columns.comment'))->sortable(),
                TextColumn::make('created')->label(__('wifi::filament/customer/pages/topup.table.columns.created'))->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('updated')->label(__('wifi::filament/customer/pages/topup.table.columns.modified'))->dateTime()->sortable()->since()->dateTimeTooltip(),
            ])
            ->filters([
                //
            ])
            ->actions([
                
            ])
            ->bulkActions([
                //
            ]);
    }
    protected function getHeaderActions(): array
    {
        return [
            Action::make('addTopup')
                ->label(__('wifi::filament/customer/pages/topup.actions.title')) // اسم الزرار المكتوب فوق
                ->icon('heroicon-o-user-plus')
                ->button() // لجعل الرابط يظهر كـ زر ملوّن بارز
                ->modalHeading(__('wifi::filament/customer/pages/topup.actions.modal_heading'))
                ->modalWidth('md')
                ->form([
                    Select::make('cloud_id')
                        ->label(__('wifi::filament/customer/pages/topup.headeractions.form.cloud'))
                        ->options(function() {
                            $user = Filament::auth()->user();
                            if (! $user) return [];
                            
                            $cloudIds = WifiPartnerCloud::where('partner_id', $user->id)->pluck('cloud_id')->toArray();
                            return Cloud::whereIn('id', $cloudIds)->pluck('name', 'id');
                        })
                        ->required(),
                    Select::make('permanent_user_id')->label(__('wifi::filament/customer/pages/topup.headeractions.form.username'))->required()->regex('/^[a-zA-Z0-9]+$/')
                        ->options(function() {
                            $user = Filament::auth()->user();
                            if (! $user) return [];
                            
                            $cloudIds = WifiPartnerCloud::where('partner_id', $user->id)->pluck('cloud_id')->toArray();
                            return PermanentUser::whereIn('cloud_id', $cloudIds)->where('profile','TopUp_U')->get()
                                ->mapWithKeys(function (PermanentUser $user) {
                                    $displayName = trim(($user->username ?? '') . ' - ' . ($user->name ?? ''));
                                    return [$user->id => $displayName !== '-' ? $displayName : (string) $user->id];
                                })
                                ->toArray();
                                })
                                ->searchable()
                                ->required(),
                    Select::make('type')
                        ->label(__('wifi::filament/customer/pages/topup.headeractions.form.type'))
                        ->options([
                            'data' => 'data',
                            'time' => 'time',
                        ])
                        ->required()
                        ->reactive()
                        ->afterStateUpdated(function (callable $set): void {
                            $set('data_unit', null);
                        }),
                    TextInput::make('value')
                        ->label(__('wifi::filament/customer/pages/topup.headeractions.form.value'))
                        ->numeric()
                        ->integer()
                        ->minValue(1)
                        ->required(),
                    Select::make('data_unit')
                        ->label(__('wifi::filament/customer/pages/topup.headeractions.form.data_unit'))
                        ->options(function (callable $get) {
                            return match ($get('type')) {
                                'data' => [
                                    'gb' => 'gb',
                                    'mb' => 'mb',
                                ],
                                'time' => [
                                    'minutes' => 'minutes',
                                    'hours' => 'hours',
                                    'days' => 'days',
                                ],
                                default => [],
                            };
                        })
                        ->disabled(fn (callable $get) => blank($get('type')))
                        ->required(),
                    Textarea::make('comment')
                        ->label(__('wifi::filament/customer/pages/topup.headeractions.form.comment'))
                        ->rows(3)
                        ->maxLength(255),
                    ])
                // الأكشن اللي هيتنفذ في الداتا بيز عند الضغط على زر الحفظ جوة الـ Modal
                ->action(function (array $data) {
                    try {
                        // 1. نداء السيرفيس وتمرير البيانات المجمعة من الفورم
                        app(PermanentUserService::class)->addtopup($data);

                        // 2. إشعار نجاح العملية
                        Notification::make()
                            ->title(__('wifi::filament/customer/pages/topup.notifications.topup_success')) // أو اكتب "تم الشحن بنجاح"
                            ->success()
                            ->send();

                    } catch (Throwable $e) {
                        // 3. التقاط أي خطأ قادم من السيرفيس (API Error) وعرضه للمستخدم بأمان
                        Notification::make()
                            ->title(__('wifi::filament/customer/pages/topup.notifications.topup_failed'))
                            ->body($e->getMessage())
                            ->danger()
                            ->send();
                    }
                }),
        ];
    }
}
