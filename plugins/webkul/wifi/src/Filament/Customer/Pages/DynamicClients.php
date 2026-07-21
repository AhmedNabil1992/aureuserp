<?php

namespace Webkul\Wifi\Filament\Customer\Pages;


use Filament\Pages\Page;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Webkul\Wifi\Models\DynamicClient;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\IconColumn;
use Filament\Support\Enums\FontWeight;
use Carbon\Carbon;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Filters\SelectFilter;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\Wifi\Filament\Customer\Concerns\HasWifiAccess;

class DynamicClients extends Page implements HasTable
{
    use InteractsWithTable,HasWifiAccess;

    protected string $view = 'wifi::filament.customer.clusters.wi-fi.pages.dynamic-clients';

    // protected static ?string $cluster = WiFiCluster::class;

    protected static ?int $navigationSort = 1;
    public static function getNavigationLabel(): string
    {
        return __('wifi::filament/customer/pages/dynamicclient.title');
    }
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-wifi';

    public static function getmodelLabel(): string
    {
        return __('wifi::filament/customer/pages/dynamicclient.title');
    }
    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.wifi');
    }

    // public static function canAccess(): bool
    // {
    //     $user = Filament::auth()->user();

    //     if (! $user) {
    //         return false;
    //     }

    //     if (! Schema::hasTable('wifi_partner_clouds')) {
    //         return false;
    //     }

    //     return WifiPartnerCloud::where('partner_id', $user->id)->exists();
    // }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        $partnerId = $user?->id;

        $query = DynamicClient::query();
        $cloudIds = [];

        if ($partnerId) {
            $cloudIds = WifiPartnerCloud::where('partner_id', $partnerId)->pluck('cloud_id')->toArray();
            $query->whereIn('cloud_id', $cloudIds);
        } else {
            $query->whereRaw('1=0');
        }

        return $table
            ->query($query)
            ->columns([
                TextColumn::make('cloud.name')->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.cloud'))->searchable()->sortable(),
                TextColumn::make('dynamicClientRealms.realm.name')->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.realm'))->searchable()->sortable()->placeholder('Available to All'),
                TextColumn::make('name')->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.name'))->searchable()->sortable(),
                TextColumn::make('nasidentifier')->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.nasidentifier'))->searchable()->sortable(),
                TextColumn::make('last_contact')->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.last_contact'))->sortable()
                    ->badge()
                    ->color(function ($state) {
                        if ($state) {
                            $now = Carbon::now('Africa/Cairo'); // Set timezone to Egypt
                            $lastContact = Carbon::parse($state, 'Africa/Cairo'); // Set timezone to Egypt
                            $diffInMinutes = $lastContact->diffInMinutes($now);
                            if ($diffInMinutes <= 60) {
                                return 'success'; // green
                            } elseif ($diffInMinutes > 60 && $diffInMinutes <= 120) {
                                return 'warning'; // yellow
                            } elseif ($diffInMinutes > 120) {
                                return 'danger'; // red
                            }
                        }
                        return 'secondary'; // gray
                    })
                    ->since()->dateTimeTooltip()->weight(FontWeight::Bold),
                TextColumn::make('last_contact_ip')->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.last_contact_ip'))->sortable(),
                IconColumn::make('picture')
                    ->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.picture'))
                    ->getStateUsing(function ($record) {
                        return ! empty($record->Picture);
                    })
                    ->boolean(),
                IconColumn::make('active')
                    ->label(__('wifi::filament/customer/pages/dynamicclient.table.columns.active'))
                    ->sortable()
                    ->boolean()
                    ->trueIcon(Heroicon::OutlinedCheckBadge)
                    ->falseIcon(Heroicon::OutlinedXMark),
            ])
            ->filters([
                SelectFilter::make('realm')
                    ->label(__('wifi::filament/customer/pages/dynamicclient.filters.realm'))
                    ->options(function () use ($cloudIds) {
                        try {
                            // استخراج كلاس الـ Realm ديناميكياً لتفادي مشاكل الـ Namespace والـ Prefixes للـ Packages
                            $dynamicClient = new DynamicClient();
                            if (! method_exists($dynamicClient, 'dynamicClientRealms')) {
                                return [];
                            }

                            $pivotModel = $dynamicClient->dynamicClientRealms()->getRelated();
                            if (! method_exists($pivotModel, 'realm')) {
                                return [];
                            }

                            $realmClass = get_class($pivotModel->realm()->getRelated());

                            // جلب أسماء الـ Realms الفريدة التي تملك بيانات تخص الـ Clouds الخاصة بالعميل الحالي فقط
                            return $realmClass::whereHas('dynamicClientRealms.dynamicClient', function ($q) use ($cloudIds) {
                                    $q->whereIn('cloud_id', $cloudIds);
                                })
                                ->pluck('name', 'name') // جعل المفتاح والقيمة الاسم للبحث المطابق بالاسم
                                ->toArray();
                        } catch (\Exception $e) {
                            return [];
                        }
                    })
                    ->query(function ($query, array $data) {
                        if (empty($data['value'])) {
                            return $query;
                        }

                        // تصفية الجدول الرئيسي بناءً على الاسم المختار من القائمة المنسدلة
                        return $query->whereHas('dynamicClientRealms.realm', function ($q) use ($data) {
                            $q->where('name', $data['value']);
                        });
                    })
            ])
            ->actions([
                Action::make('edit_picture')
                    ->label(__('wifi::filament/customer/pages/dynamicclient.actions.title'))
                    ->modalHeading(__('wifi::filament/customer/pages/dynamicclient.actions.modal_heading'))
                    ->modalWidth('md')
                    // السطر ده عشان يقرأ الصورة القديمة ويوريها للمستخدم أول ما يفتح الـ Modal
                    ->fillForm(fn ($record) => [
                        'Picture' => $record->Picture,
                    ])
                    ->form([
                        FileUpload::make('Picture')
                            ->label(__('wifi::filament/customer/pages/dynamicclient.actions.fileupload_placeholder'))
                            ->image()
                            ->directory('dynamic-clients')
                            ->maxSize(1024)
                    ])
                    // هنا بنقول للنظام يعمل إيه لما المستخدم يضغط على زر الحفظ جوة الـ Modal
                    ->action(function ($record, array $data) {
                        $record->update([
                            'Picture' => $data['Picture'],
                        ]);
                    }),
            ])
            ->bulkActions([
                //
            ]);
    }


}
