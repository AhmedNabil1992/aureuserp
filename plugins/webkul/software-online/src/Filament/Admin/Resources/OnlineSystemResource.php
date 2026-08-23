<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource\Pages\CreateOnlineSystem;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource\Pages\EditOnlineSystem;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineSystemResource\Pages\ListOnlineSystems;
use Webkul\SoftwareOnline\Models\OnlineSystem;
use Webkul\SoftwareOnline\Services\OnlineSystemProvisioningService;

class OnlineSystemResource extends Resource
{
    protected static ?string $model = OnlineSystem::class;

    protected static ?string $slug = 'online-systems';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/admin/resources/system.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('software-online::filament/admin/resources/system.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software-online::filament/admin/resources/system.models.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('software-online::filament/admin/resources/system.sections.general'))
                ->schema([
                    TextInput::make('name')
                        ->label(__('software-online::filament/admin/resources/system.fields.name'))
                        ->required()
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', Str::slug($state))),
                    TextInput::make('slug')
                        ->label(__('software-online::filament/admin/resources/system.fields.slug'))
                        ->required()
                        ->unique(ignoreRecord: true),
                    TextInput::make('base_url')
                        ->label(__('software-online::filament/admin/resources/system.fields.base_url'))
                        ->placeholder('https://{subdomain}.poscloud.com')
                        ->helperText(__('software-online::filament/admin/resources/system.fields.base_url_helper')),
                    TextInput::make('icon')
                        ->label(__('software-online::filament/admin/resources/system.fields.icon'))
                        ->placeholder('heroicon-o-shopping-bag'),
                    Textarea::make('description')
                        ->label(__('software-online::filament/admin/resources/system.fields.description'))
                        ->columnSpanFull(),
                    Toggle::make('is_active')
                        ->label(__('software-online::filament/admin/resources/system.fields.is_active'))
                        ->default(true),
                    TextInput::make('sort_order')
                        ->label(__('software-online::filament/admin/resources/system.fields.sort_order'))
                        ->numeric()
                        ->default(0),
                ])->columns(2),

            Section::make(__('software-online::filament/admin/resources/system.sections.api_config'))
                ->schema([
                    TextInput::make('api_base_url')
                        ->label(__('software-online::filament/admin/resources/system.fields.api_base_url'))
                        ->placeholder('https://api.poscloud.com')
                        ->columnSpanFull(),
                    TextInput::make('api_token')
                        ->label(__('software-online::filament/admin/resources/system.fields.api_token'))
                        ->password()
                        ->revealable()
                        ->columnSpanFull(),
                    KeyValue::make('api_headers')
                        ->label(__('software-online::filament/admin/resources/system.fields.api_headers'))
                        ->columnSpanFull(),
                    TextInput::make('create_tenant_endpoint')
                        ->label(__('software-online::filament/admin/resources/system.fields.create_endpoint'))
                        ->default('/api/v1/tenants'),
                    TextInput::make('renew_tenant_endpoint')
                        ->label(__('software-online::filament/admin/resources/system.fields.renew_endpoint'))
                        ->default('/api/v1/tenants/{tenant_id}/renew'),
                    TextInput::make('suspend_tenant_endpoint')
                        ->label(__('software-online::filament/admin/resources/system.fields.suspend_endpoint'))
                        ->default('/api/v1/tenants/{tenant_id}/suspend'),
                    TextInput::make('activate_tenant_endpoint')
                        ->label(__('software-online::filament/admin/resources/system.fields.activate_endpoint'))
                        ->default('/api/v1/tenants/{tenant_id}/activate'),
                    TextInput::make('sync_status_endpoint')
                        ->label(__('software-online::filament/admin/resources/system.fields.sync_endpoint'))
                        ->default('/api/v1/tenants/{tenant_id}/status'),
                ])->columns(2)->collapsed(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label(__('software-online::filament/admin/resources/system.fields.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('slug')
                    ->label(__('software-online::filament/admin/resources/system.fields.slug'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('plans_count')
                    ->label(__('software-online::filament/admin/resources/system.fields.plans_count'))
                    ->counts('plans')
                    ->badge()
                    ->color('info'),
                TextColumn::make('instances_count')
                    ->label(__('software-online::filament/admin/resources/system.fields.instances_count'))
                    ->counts('instances')
                    ->badge()
                    ->color('success'),
                IconColumn::make('is_active')
                    ->label(__('software-online::filament/admin/resources/system.fields.is_active'))
                    ->boolean(),
                TextColumn::make('updated_at')
                    ->label(__('software-online::filament/admin/resources/system.fields.updated_at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                Action::make('testApi')
                    ->label(__('software-online::filament/admin/resources/system.actions.test_api'))
                    ->icon('heroicon-o-signal')
                    ->color('warning')
                    ->action(function (OnlineSystem $record) {
                        $result = app(OnlineSystemProvisioningService::class)->testConnection($record);
                        if ($result['success']) {
                            Notification::make()
                                ->title(__('software-online::filament/admin/resources/system.notifications.api_connected'))
                                ->body($result['message'])
                                ->success()
                                ->send();
                        } else {
                            Notification::make()
                                ->title(__('software-online::filament/admin/resources/system.notifications.api_failed'))
                                ->body($result['message'])
                                ->danger()
                                ->send();
                        }
                    }),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOnlineSystems::route('/'),
            'create' => CreateOnlineSystem::route('/create'),
            'edit'   => EditOnlineSystem::route('/{record}/edit'),
        ];
    }
}
