<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Wifi\Models\PermanentUser;
use Filament\Tables\Columns\TextColumn;
use Filament\Actions\Action;
use Filament\Tables\Columns\IconColumn;
use Webkul\Wifi\Services\PermanentUserService;
use Filament\Notifications\Notification;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Select;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\Profile;

class PermanentUsers extends Page implements HasTable
{
    use InteractsWithTable;

    protected string $view = 'wifi::filament.customer.clusters.wi-fi.pages.permanent-users';

    // protected static ?string $cluster = WiFiCluster::class;

    protected static ?string $title = 'Permanent Users';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-users';

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

        $query = PermanentUser::query();

        if ($partnerId) {
            $cloudIds = WifiPartnerCloud::where('partner_id', $partnerId)->pluck('cloud_id')->toArray();
            $query->whereIn('cloud_id', $cloudIds);
        } else {
            $query->whereRaw('1=0');
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('cloud.name')->label('Cloud'),    
                TextColumn::make('realms.name')->label('Realm'),
                TextColumn::make('username')->searchable()->sortable(),
                TextColumn::make('profiles.name')->label('Profile'),
                TextColumn::make('last_accept_time')->label('Last Accept Time')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('last_reject_time')->label('Last Reject Time')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('last_accept_nas')->label('Last Accept NAS')->searchable()->sortable(),
                TextColumn::make('last_reject_nas')->label('Last Reject NAS')->searchable()->sortable(),
                TextColumn::make('last_reject_message')->label('Last Reject Message')->searchable()->sortable(),
                TextColumn::make('created')->label('Created')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('modified')->label('Modified')->dateTime()->sortable()->since()->dateTimeTooltip(),
                IconColumn::make('active')->label('Active')->boolean()->sortable(),
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('delete')
                    ->label('Delete')
                    ->icon('heroicon-o-trash')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->action(function (PermanentUser $record) {
                        app(PermanentUserService::class)->delete($record);
                        Notification::make()
                            ->title('Permanent User deleted successfully')
                            ->success()
                            ->send();
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('create_user')
                ->label('إضافة مستخدم جديد') // اسم الزرار المكتوب فوق
                ->icon('heroicon-o-user-plus')
                ->button() // لجعل الرابط يظهر كـ زر ملوّن بارز
                ->modalHeading('إنشاء مستخدم دائم جديد')
                ->modalWidth('md')
                ->form([
                    TextInput::make('username')->label('اسم المستخدم')->required()->regex('/^[a-zA-Z0-9]+$/')
                        ->minLength(5)
                        ->maxLength(20)
                        ->unique(PermanentUser::class, 'username', fn($record) => $record ? $record->id : null)
                        ->helperText('يجب أن يتكون من حروف وأرقام فقط،لا يقل عن 5 احرف ولا يزيد عن 20 حرفًا'),
                    TextInput::make('password')->label('كلمة المرور')->required()
                        ->regex('/^[a-zA-Z0-9]+$/')
                        ->minLength(5)
                        ->maxLength(20)
                        ->helperText('يجب أن تتكون من حروف وأرقام فقط،لا يقل عن 5 احرف ولا يزيد عن 20 حرفًا'),

                    // حقل اختيار الـ Cloud المتاحة لهذا الـ Partner فقط
                    Select::make('cloud_id')
                        ->label('السحابة')
                        ->options(function() {
                            $user = Filament::auth()->user();
                            if (! $user) return [];
                            
                            $cloudIds = WifiPartnerCloud::where('partner_id', $user->id)->pluck('cloud_id')->toArray();
                            return Cloud::whereIn('id', $cloudIds)->pluck('name', 'id');
                        })
                        ->required(),

                    // حقل اختيار الـ Realm المتاحة للـ Clouds الخاصة بالـ Partner فقط لضمان الأمان
                    Select::make('realm_id')
                        ->label('Realm')
                        ->options(function() {
                            $user = Filament::auth()->user();
                            if (! $user) return [];

                            $cloudIds = WifiPartnerCloud::where('partner_id', $user->id)->pluck('cloud_id')->toArray();
                            return Realm::whereIn('cloud_id', $cloudIds)->pluck('name', 'id');
                        })
                        ->required(),

                    // حقل اختيار الـ Profile المتاحة للـ Clouds الخاصة بالـ Partner
                    Select::make('profile_id')
                        ->label('Profile')
                        ->options(function() {
                            $user = Filament::auth()->user();
                            if (! $user) return [];

                            $cloudIds = WifiPartnerCloud::where('partner_id', $user->id)->pluck('cloud_id')->toArray();
                            return Profile::whereIn('id', [
                                334,
                                1292,
                                1293,
                                632,
                                288,
                                1570,
                            ])->pluck('name', 'id');
                        })
                        ->required(),
                ])
                // الأكشن اللي هيتنفذ في الداتا بيز عند الضغط على زر الحفظ جوة الـ Modal
                ->action(function (array $data) {
                    PermanentUser::create([
                        'username'   => $data['username'],
                        'cloud_id'   => $data['cloud_id'],
                        'realm_id'   => $data['realm_id'],
                        'profile_id' => $data['profile_id'],
                        'active'     => true, // تفعيله تلقائياً عند الإنشاء
                    ]);

                    Notification::make()
                        ->title('تم إنشاء المستخدم بنجاح')
                        ->success()
                        ->send();
                }),
        ];
    }
}
