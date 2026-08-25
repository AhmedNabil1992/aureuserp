<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Pages;

use BackedEnum;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Illuminate\Support\Collection;
use Webkul\Partner\Enums\AccountType;
use Webkul\Partner\Models\Partner;
use Webkul\Partner\Models\Tag;
use Webkul\Support\Enums\NavigationGroup;

class SendCustomerBroadcast extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-megaphone';

    protected static ?string $slug = 'technical-support/broadcast';

    protected static ?int $navigationSort = 11;

    protected string $view = 'technical-support::filament.admin.pages.send-customer-broadcast';

    public ?array $data = [];

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::TechnicalSupport;
    }

    public static function getNavigationLabel(): string
    {
        return __('إرسال إشعارات للعملاء');
    }

    public function getTitle(): string
    {
        return __('إرسال إشعارات ورسائل للعملاء');
    }

    public function mount(): void
    {
        $this->form->fill([
            'target_type'       => 'all',
            'partner_ids'       => [],
            'tag_ids'           => [],
            'type'              => 'info',
            'title'             => '',
            'body'              => '',
            'action_url'        => '',
            'action_label'      => 'عرض التفاصيل',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                // 1. Audience Targeting Section
                Section::make('الفئة المستهدفة')
                    ->description('حدد العملاء الذين ترغب في إرسال الإشعار إليهم')
                    ->icon('heroicon-o-user-group')
                    ->schema([
                        Radio::make('target_type')
                            ->label('طريقة تحديد المستلمين')
                            ->options([
                                'all'      => '📢 جميع العملاء المسجلين',
                                'selected' => '👤 تحديد عملاء بالاسم (اختيار يدوي)',
                                'tags'     => '🏷️ تحديد حسب الوسوم / تصنيفات العملاء (Tags)',
                            ])
                            ->default('all')
                            ->live()
                            ->columnSpanFull(),

                        Select::make('partner_ids')
                            ->label('العملاء المستهدفين')
                            ->placeholder('اختر عميل أو أكثر...')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function (): array {
                                return Partner::query()
                                    ->where('account_type', '!=', AccountType::ADDRESS)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->toArray();
                            })
                            ->visible(fn (Get $get): bool => $get('target_type') === 'selected')
                            ->required(fn (Get $get): bool => $get('target_type') === 'selected')
                            ->columnSpanFull(),

                        Select::make('tag_ids')
                            ->label('وسوم وتصنيفات العملاء (Tags)')
                            ->placeholder('اختر وسم أو أكثر...')
                            ->multiple()
                            ->searchable()
                            ->preload()
                            ->options(function (): array {
                                return Tag::orderBy('name')->pluck('name', 'id')->toArray();
                            })
                            ->visible(fn (Get $get): bool => $get('target_type') === 'tags')
                            ->required(fn (Get $get): bool => $get('target_type') === 'tags')
                            ->columnSpanFull(),
                    ])
                    ->columns(2),

                // 2. Notification Content Section
                Section::make('محتوى الإشعار والرسالة')
                    ->description('اكتب تفاصيل الرسالة التي ستظهر في شريط الإشعارات لدى العميل')
                    ->icon('heroicon-o-envelope')
                    ->schema([
                        TextInput::make('title')
                            ->label('عنوان الإشعار')
                            ->placeholder('مثال: تحديث هام في النظام / صيانة مجدولة...')
                            ->required()
                            ->maxLength(255)
                            ->columnSpan(2),

                        Select::make('type')
                            ->label('نوع الإشعار ولونه')
                            ->options([
                                'info'    => 'ℹ️ معلومات عامة (أزرق)',
                                'success' => '✅ إعلان جديد / ميزة (أخضر)',
                                'warning' => '⚠️ تنبيه / صيانة (برتقالي)',
                                'danger'  => '🚨 عاجل وهام (أحمر)',
                            ])
                            ->default('info')
                            ->required()
                            ->columnSpan(1),

                        Textarea::make('body')
                            ->label('نص الرسالة / تفاصيل الإشعار')
                            ->placeholder('اكتب نص الإشعار هنا بالتفصيل...')
                            ->rows(4)
                            ->required()
                            ->columnSpanFull(),

                        TextInput::make('action_url')
                            ->label('رابط توجيه اختياري (Action URL)')
                            ->placeholder('مثال: /portal/support-tickets أو https://example.com')
                            ->columnSpan(2),

                        TextInput::make('action_label')
                            ->label('نص الزر')
                            ->default('عرض التفاصيل')
                            ->columnSpan(1),
                    ])
                    ->columns(3),
            ])
            ->statePath('data');
    }

    public function send(): void
    {
        $state = $this->form->getState();

        // 1. Resolve Target Partners Collection
        /** @var Collection<int, Partner> $partners */
        $partners = match ($state['target_type'] ?? 'all') {
            'selected' => Partner::whereIn('id', $state['partner_ids'] ?? [])->get(),
            'tags'     => Partner::whereHas('tags', fn ($q) => $q->whereIn('partners_tags.id', $state['tag_ids'] ?? []))->get(),
            default    => Partner::where('account_type', '!=', AccountType::ADDRESS)->get(),
        };

        if ($partners->isEmpty()) {
            Notification::make()
                ->title('لم يتم العثور على عملاء!')
                ->body('لم يتم العثور على أي عميل يطابق الشروط المحددة.')
                ->warning()
                ->send();

            return;
        }

        // 2. Build Notification
        $icon = match ($state['type'] ?? 'info') {
            'success' => 'heroicon-o-check-circle',
            'warning' => 'heroicon-o-exclamation-triangle',
            'danger'  => 'heroicon-o-exclamation-circle',
            default   => 'heroicon-o-information-circle',
        };

        $notification = Notification::make()
            ->title($state['title'])
            ->body($state['body'])
            ->icon($icon)
            ->status($state['type'] ?? 'info');

        if (! empty($state['action_url'])) {
            $notification->actions([
                Action::make('view')
                    ->label($state['action_label'] ?: 'عرض التفاصيل')
                    ->url($state['action_url'])
                    ->button(),
            ]);
        }

        // 3. Send to Database & Broadcast to each partner
        $sentCount = 0;
        foreach ($partners as $partner) {
            try {
                $partner->notifyNow($notification->toDatabase());
                $sentCount++;

                // Broadcast WebSockets
                try {
                    $notification->broadcast($partner);
                } catch (\Throwable) {}
            } catch (\Throwable) {}
        }

        // 4. Feedback Notification to Admin
        Notification::make()
            ->title('تم إرسال الإشعار بنجاح! 🚀')
            ->body("تم إرسال الإشعار لـ {$sentCount} عميل بنجاح.")
            ->success()
            ->send();

        // 5. Reset Message Fields
        $this->form->fill([
            'target_type'  => $state['target_type'],
            'partner_ids'  => $state['partner_ids'] ?? [],
            'tag_ids'      => $state['tag_ids'] ?? [],
            'type'         => 'info',
            'title'        => '',
            'body'         => '',
            'action_url'   => '',
            'action_label' => 'عرض التفاصيل',
        ]);
    }
}
