<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Pages;

use BackedEnum;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Webkul\Support\Enums\NavigationGroup;
use Webkul\TechnicalSupport\Settings\SupportAutoReplySettings;

class ManageSupportAutoReplySettings extends SettingsPage
{
    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-clock';

    protected static ?string $slug = 'technical-support/settings';

    protected static ?int $navigationSort = 10;

    protected static string $settings = SupportAutoReplySettings::class;

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::TechnicalSupport;
    }

    public static function getNavigationLabel(): string
    {
        return __('technical-support::filament/admin/pages/settings.navigation.label');
    }

    public function getTitle(): string
    {
        return __('technical-support::filament/admin/pages/settings.title');
    }

    public function form(Schema $schema): Schema
    {
        return $schema->components([
            // 1. Emergency Mode Section
            Section::make(__('technical-support::filament/admin/pages/settings.sections.emergency.title'))
                ->description(__('technical-support::filament/admin/pages/settings.sections.emergency.description'))
                ->icon('heroicon-o-exclamation-triangle')
                ->schema([
                    Toggle::make('is_emergency_mode')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.is_emergency_mode'))
                        ->helperText(__('technical-support::filament/admin/pages/settings.helpers.is_emergency_mode'))
                        ->onColor('danger')
                        ->live(),

                    Textarea::make('emergency_message')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.emergency_message'))
                        ->rows(3)
                        ->required(fn (Get $get): bool => (bool) $get('is_emergency_mode'))
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // 2. Welcome Auto-Reply Section
            Section::make(__('technical-support::filament/admin/pages/settings.sections.welcome.title'))
                ->description(__('technical-support::filament/admin/pages/settings.sections.welcome.description'))
                ->icon('heroicon-o-chat-bubble-left-ellipsis')
                ->schema([
                    Toggle::make('is_auto_reply_enabled')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.is_auto_reply_enabled'))
                        ->helperText(__('technical-support::filament/admin/pages/settings.helpers.is_auto_reply_enabled'))
                        ->default(true),

                    Textarea::make('welcome_message')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.welcome_message'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),

            // 3. Business Hours & Off-Hours Section
            Section::make(__('technical-support::filament/admin/pages/settings.sections.business_hours.title'))
                ->description(__('technical-support::filament/admin/pages/settings.sections.business_hours.description'))
                ->icon('heroicon-o-calendar-days')
                ->schema([
                    Toggle::make('is_business_hours_enabled')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.is_business_hours_enabled'))
                        ->helperText(__('technical-support::filament/admin/pages/settings.helpers.is_business_hours_enabled'))
                        ->default(true)
                        ->columnSpanFull(),

                    CheckboxList::make('work_days')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.work_days'))
                        ->options([
                            0 => 'الأحد (Sunday)',
                            1 => 'الاثنين (Monday)',
                            2 => 'الثلاثاء (Tuesday)',
                            3 => 'الأربعاء (Wednesday)',
                            4 => 'الخميس (Thursday)',
                            5 => 'الجمعة (Friday)',
                            6 => 'السبت (Saturday)',
                        ])
                        ->columns(4)
                        ->columnSpanFull(),

                    TimePicker::make('work_start_time')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.work_start_time'))
                        ->seconds(false)
                        ->default('09:00'),

                    TimePicker::make('work_end_time')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.work_end_time'))
                        ->seconds(false)
                        ->default('18:00'),

                    Select::make('timezone')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.timezone'))
                        ->options([
                            'Africa/Cairo'     => 'القاهرة (Africa/Cairo - GMT+2/3)',
                            'Asia/Riyadh'      => 'الرياض (Asia/Riyadh - GMT+3)',
                            'Asia/Dubai'       => 'دبي (Asia/Dubai - GMT+4)',
                            'Asia/Kuwait'      => 'الكويت (Asia/Kuwait - GMT+3)',
                            'Asia/Amman'       => 'عَمّان (Asia/Amman - GMT+3)',
                            'Africa/Casablanca'=> 'الدار البيضاء (Africa/Casablanca - GMT+1)',
                            'UTC'              => 'توقيت غرينتش (UTC)',
                        ])
                        ->searchable()
                        ->default('Africa/Cairo'),

                    Textarea::make('out_of_hours_message')
                        ->label(__('technical-support::filament/admin/pages/settings.fields.out_of_hours_message'))
                        ->helperText(__('technical-support::filament/admin/pages/settings.helpers.out_of_hours_message'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(3),
        ]);
    }
}
