<?php

namespace Webkul\Wifi\Filament\Customer\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Schema;
use Filament\Tables\Table;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Facades\Filament;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Filament\Tables\Columns\TextColumn;
use Webkul\Wifi\Models\Voucher;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Filters\SelectFilter; // استدعاء كلاس الفلتر
use Filament\Tables\Enums\FiltersLayout;   // التحكم بمكان الفلتر
use Filament\Actions\Action;               // كلاس الأكشن الموحد لـ V5
use Filament\Forms\Components\Placeholder; // لعرض جدول الـ POPUP المخصص
use Illuminate\Support\HtmlString;         // لتنسيق الـ HTML بأمان

class VouchersInfo extends Page implements HasTable
{
    use InteractsWithTable;
    protected string $view = 'wifi::filament.customer.pages.vouchers-info';

    public string $activeTab = 'all';
    protected static ?string $title = 'Vouchers Info';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-information-circle';
    
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

    public function switchTab(string $tab): void
    {
        $this->activeTab = $tab;
        if (method_exists($this, 'resetPage')) {
            $this->resetPage('page');      // لتهيئة الصفحات إذا كان المؤشر الافتراضي page
            $this->resetPage('tablePage'); // لتهيئة الصفحات إذا كان الجدول مسمى داخلياً tablePage
        }
    }

    public function getLabel(string $key): string
    {
        $locale = app()->getLocale();
        
        $translations = [
            'ar' => [
                'all'      => 'الكل',
                'new'      => 'جديد',
                'used'     => 'مستخدم',
                'depleted' => 'مستنفذ',
                'expired'  => 'منتهي', // تم تصحيح "إنتهاي" هنا
            ],
            'en' => [
                'all'      => 'All',
                'new'      => 'New',
                'used'     => 'Used',
                'depleted' => 'Depleted',
                'expired'  => 'Expired',
            ]
        ];

        return $translations[$locale][$key] ?? $translations['en'][$key] ?? $key;
    }

    public function getStatusCounts(): array
    {
        $user = Filament::auth()->user();
        if (! $user) return ['all' => 0, 'new' => 0, 'used' => 0, 'depleted' => 0, 'expired' => 0];

        $cloudIds = WifiPartnerCloud::where('partner_id', $user->id)->pluck('cloud_id')->toArray();

        $counts = Voucher::whereIn('cloud_id', $cloudIds)
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'all'      => array_sum($counts),
            'new'      => $counts['new'] ?? 0,
            'used'     => $counts['used'] ?? 0,
            'depleted' => $counts['depleted'] ?? 0,
            'expired'  => $counts['expired'] ?? 0,
        ];
    }

    public function table(Table $table): Table
    {
        $user = Filament::auth()->user();
        $partnerId = $user?->id;

        $query = Voucher::query();

        if ($partnerId) {
            $cloudIds = WifiPartnerCloud::where('partner_id', $partnerId)->pluck('cloud_id')->toArray();
            $query->whereIn('cloud_id', $cloudIds);
        } else {
            $query->whereRaw('1=0');
        }

        if ($this->activeTab !== 'all') {
            $query->where('status', $this->activeTab);
        }
        return $table
            ->query($query)
            ->columns([
                TextColumn::make('cloud.name')->label('Cloud'),    
                TextColumn::make('realm')->label('Realm'),
                TextColumn::make('batch')->label('إسم الملف')->sortable()->searchable(),
                TextColumn::make('name')->searchable()->sortable(),
                TextColumn::make('profile')->label('Profile'),
                TextColumn::make('status')->label('Status')->badge()->sortable()->color(function ($state) {
                        if ($state) {
                            if ($state == 'new') {
                                return 'success'; // green
                            } elseif ($state == 'used') {
                                return 'warning'; // yellow
                            } elseif ($state == 'expired') {
                                return 'danger'; // red
                            } elseif ($state == 'depleted') {
                                return 'info'; // blue
                            }
                        }
                        return 'secondary'; // gray
                    })
                    ->formatStateUsing(function ($state) {
                        $replacements = [
                            'new' => 'جديد',
                            'depleted' => 'إستفذ',
                            'used' => 'مستخدم',
                            'expired' => 'منتهي',

                        ];
                        return $replacements[$state] ?? $state;
                    }),
                TextColumn::make('perc_time_used')
                    ->label('نسبة الوقت المستخدم')
                    ->badge()
                    ->color(function ($state) {
                        if ($state <= 25) {
                            return 'success'; // red
                        } elseif ($state <= 50) {
                            return 'info'; // yellow
                        } elseif ($state <= 75) {
                            return 'warning'; // blue
                        } else {
                            return 'danger'; // green
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        return $state . '%';
                    }),
                TextColumn::make('perc_data_used')->label('نسبة البيانات المستخدمة')
                    ->badge()
                    ->color(function ($state) {
                        if ($state <= 25) {
                            return 'success'; // red
                        } elseif ($state <= 50) {
                            return 'info'; // yellow
                        } elseif ($state <= 75) {
                            return 'warning'; // blue
                        } else {
                            return 'danger'; // green
                        }
                    })
                    ->formatStateUsing(function ($state) {
                        return $state . '%';
                    }),
                TextColumn::make('last_accept_time')->label('Last Accept Time')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('last_reject_time')->label('Last Reject Time')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('last_accept_nas')->label('Last Accept NAS')->searchable()->sortable(),
                TextColumn::make('last_reject_nas')->label('Last Reject NAS')->searchable()->sortable(),
                TextColumn::make('last_reject_message')->label('Last Reject Message')->sortable(),
                TextColumn::make('expire')->label('تاريخ الانتهاء')->since()->dateTimeTooltip()->weight(FontWeight::Bold),
                TextColumn::make('time_valid')->label('وقت الصلاحية'),
                TextColumn::make('created')->label('Created')->dateTime()->sortable()->since()->dateTimeTooltip(),
                TextColumn::make('modified')->label('Modified')->dateTime()->sortable()->since()->dateTimeTooltip(),
            ])
            ->filters([
                
            ])
            ->actions([
                // 2. إضافة أكشن الـ POPUP لعرض تفاصيل استهلاك الراديوس (Radacct)
                Action::make('view_usage')
                    ->label('تفاصيل الاستهلاك')
                    ->icon('heroicon-o-chart-bar')
                    ->color('info')
                    ->modalHeading(fn ($record) => "سجل استهلاك الكارت: {$record->name}")
                    ->modalWidth('5xl')
                    ->modalSubmitAction(false) // إخفاء زر الحفظ لأنه عرض فقط
                    ->modalCancelActionLabel('إغلاق')
                    ->form(function ($record) {
                        // جلب الجلسات الخاصة بـ username الكارت الحالي من موديل Radacct
                        $sessions = \Webkul\Wifi\Models\Radacct::where('username', $record->name)
                            ->orderBy('acctstarttime', 'desc')
                            ->get();

                        return [
                            Placeholder::make('usage_table')
                                ->label('')
                                ->content(function () use ($sessions) {
                                    if ($sessions->isEmpty()) {
                                        return new HtmlString('<div class="p-4 text-center text-gray-500 dark:text-gray-400">لا توجد سجلات استهلاك أو اتصالات نشطة لهذا الكارت حتى الآن.</div>');
                                    }

                                    // بناء جدول Tailwind CSS متوافق مع النظام والـ Dark Mode
                                    $html = '<div class="overflow-x-auto border border-gray-200 dark:border-gray-700 rounded-lg">';
                                    $html .= '<table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700 text-sm text-right dir-rtl" style="direction: rtl;">';
                                    $html .= '<thead class="bg-gray-50 dark:bg-gray-800 text-gray-700 dark:text-gray-300 font-bold">';
                                    $html .= '<tr>
                                        <th class="px-4 py-3 text-center">عنوان الماك (MAC)</th>
                                        <th class="px-4 py-3">وقت بدء الاتصال</th>
                                        <th class="px-4 py-3">وقت الانتهاء</th>
                                        <th class="px-4 py-3 text-center">مدة الجلسة</th>
                                        <th class="px-4 py-3 text-center">التحميل (Download)</th>
                                        <th class="px-4 py-3 text-center">الرفع (Upload)</th>
                                    </tr></thead>';
                                    $html .= '<tbody class="divide-y divide-gray-200 dark:divide-gray-700 text-gray-600 dark:text-gray-400">';

                                    foreach ($sessions as $session) {
                                        // حساب الوقت والترافيك المستهلك
                                        $duration = $session->acctsessiontime ? gmdate("H:i:s", $session->acctsessiontime) : '00:00:00';
                                        $download = number_format($session->acctoutputoctets / (1024 * 1024), 2) . ' MB';
                                        $upload = number_format($session->acctinputoctets / (1024 * 1024), 2) . ' MB';
                                        
                                        $startTime = $session->acctstarttime ? $session->acctstarttime->format('Y-m-d H:i:s') : '-';
                                        $stopTime = $session->acctstoptime ? $session->acctstoptime->format('Y-m-d H:i:s') : '<span class="text-success-600 dark:text-success-400 font-bold animation-pulse">متصل حالياً</span>';

                                        $html .= "<tr class='hover:bg-gray-50 dark:hover:bg-gray-900/50'>
                                            <td class='px-4 py-2 text-center font-mono text-xs'>{$session->callingstationid}</td>
                                            <td class='px-4 py-2'>{$startTime}</td>
                                            <td class='px-4 py-2'>{$stopTime}</td>
                                            <td class='px-4 py-2 text-center font-mono'>{$duration}</td>
                                            <td class='px-4 py-2 text-center text-info-600 dark:text-info-400 font-mono'>{$download}</td>
                                            <td class='px-4 py-2 text-center text-warning-600 dark:text-warning-400 font-mono'>{$upload}</td>
                                        </tr>";
                                    }

                                    $html .= '</tbody></table></div>';
                                    return new HtmlString($html);
                                })
                        ];
                    })
            ])
            ->bulkActions([
                //
            ]);
    }


}
