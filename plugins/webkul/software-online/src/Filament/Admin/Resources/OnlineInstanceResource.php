<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Validation\Rules\Unique;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\CreateOnlineInstance;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\EditOnlineInstance;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\ListOnlineInstances;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\ViewOnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystem;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\SoftwareOnline\Rules\Subdomain;
use Webkul\SoftwareOnline\Services\OnlineBillingService;
use Webkul\SoftwareOnline\Services\OnlineSystemProvisioningService;
use Webkul\Support\Enums\NavigationGroup;

class OnlineInstanceResource extends Resource
{
    protected static ?string $model = OnlineInstance::class;

    protected static ?string $slug = 'online-instances';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/admin/resources/instance.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('software-online::filament/admin/resources/instance.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software-online::filament/admin/resources/instance.models.plural');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('software-online::filament/admin/resources/instance.sections.general'))
                ->schema([
                    Select::make('partner_id')
                        ->label(__('software-online::filament/admin/resources/instance.fields.customer'))
                        ->relationship('partner', 'name')
                        ->required()
                        ->searchable()
                        ->preload(),
                    Select::make('system_id')
                        ->label(__('software-online::filament/admin/resources/instance.fields.system'))
                        ->relationship('system', 'name')
                        ->required()
                        ->live()
                        ->afterStateUpdated(function (Get $get, Set $set, ?string $state, string $operation): void {
                            if ($operation !== 'create') {
                                return;
                            }

                            $set('plan_id', null);
                            static::updateCreateUrlPreview($get, $set);
                        })
                        ->searchable()
                        ->preload(),
                    Select::make('plan_id')
                        ->label(__('software-online::filament/admin/resources/instance.fields.plan'))
                        ->options(function (callable $get) {
                            $systemId = $get('system_id');
                            if (! $systemId) {
                                return [];
                            }

                            return OnlineSystemPlan::where('system_id', $systemId)->pluck('name', 'id');
                        })
                        ->required()
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set, string $operation) => $operation === 'create'
                            ? static::updateCreateSubscriptionPreview($get, $set)
                            : null)
                        ->searchable(),
                    TextInput::make('name')
                        ->label(__('software-online::filament/admin/resources/instance.fields.name'))
                        ->required(),
                    TextInput::make('subdomain')
                        ->label(__('software-online::filament/admin/resources/instance.fields.subdomain'))
                        ->placeholder('my-store')
                        ->required()
                        ->live(debounce: 400)
                        ->maxLength(50)
                        ->extraInputAttributes([
                            'autocapitalize' => 'none',
                            'autocomplete'   => 'off',
                            'spellcheck'     => 'false',
                        ])
                        ->mutateStateForValidationUsing(fn (mixed $state): string => strtolower(trim((string) $state)))
                        ->dehydrateStateUsing(fn (mixed $state): string => strtolower(trim((string) $state)))
                        ->rules([new Subdomain])
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('system_id', $get('system_id')),
                        )
                        ->afterStateUpdated(function (Get $get, Set $set, mixed $state, string $operation): void {
                            if ($operation !== 'create') {
                                return;
                            }

                            $normalized = strtolower(trim((string) $state));
                            if ($normalized !== $state) {
                                $set('subdomain', $normalized);
                            }

                            static::updateCreateUrlPreview($get, $set);
                        }),
                    TextInput::make('custom_domain')
                        ->label(__('software-online::filament/admin/resources/instance.fields.custom_domain'))
                        ->placeholder('store.example.com'),
                    TextInput::make('instance_url')
                        ->label(__('software-online::filament/admin/resources/instance.fields.instance_url'))
                        ->readOnly()
                        ->afterStateHydrated(function (TextInput $component, ?OnlineInstance $record): void {
                            if ($record) {
                                $component->state($record->full_url);
                            }
                        })
                        ->placeholder('https://my-store.example.com/admin/login')
                        ->columnSpanFull(),
                ])->columns(3),

            Section::make(__('software-online::filament/admin/resources/instance.sections.subscription'))
                ->schema([
                    Select::make('status')
                        ->label(__('software-online::filament/admin/resources/instance.fields.status'))
                        ->options(InstanceStatus::class)
                        ->default(InstanceStatus::Pending)
                        ->required()
                        ->hidden(fn (string $operation): bool => $operation === 'create'),
                    Select::make('billing_cycle')
                        ->label(__('software-online::filament/admin/resources/instance.fields.billing_cycle'))
                        ->options(BillingCycle::class)
                        ->default(BillingCycle::Monthly)
                        ->live()
                        ->afterStateUpdated(fn (Get $get, Set $set, string $operation) => $operation === 'create'
                            ? static::updateCreateSubscriptionPreview($get, $set)
                            : null)
                        ->required(),
                    TextInput::make('price')
                        ->label(__('software-online::filament/admin/resources/instance.fields.price'))
                        ->numeric()
                        ->prefix('EGP')
                        ->readOnly(fn (string $operation): bool => $operation === 'create')
                        ->default(0.00),
                    DateTimePicker::make('starts_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.starts_at'))
                        ->default(now())
                        ->required()
                        ->hidden(fn (string $operation): bool => $operation === 'create'),
                    DateTimePicker::make('expires_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.expires_at'))
                        ->required()
                        ->hidden(fn (string $operation): bool => $operation === 'create')
                        ->after('starts_at'),
                    Toggle::make('auto_renew')
                        ->label(__('software-online::filament/admin/resources/instance.fields.auto_renew'))
                        ->default(true),
                ])->columns(3),

            Section::make(__('software-online::filament/admin/resources/instance.sections.remote_sync'))
                ->schema([
                    TextInput::make('remote_tenant_id')
                        ->label(__('software-online::filament/admin/resources/instance.fields.remote_tenant_id')),
                    TextInput::make('last_api_error')
                        ->label(__('software-online::filament/admin/resources/instance.fields.last_api_error'))
                        ->disabled()
                        ->columnSpanFull(),
                    KeyValue::make('remote_data')
                        ->label(__('software-online::filament/admin/resources/instance.fields.remote_data'))
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed()
                ->hidden(fn (string $operation): bool => $operation === 'create'),
        ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('software-online::filament/admin/resources/instance.sections.general'))
                ->schema([
                    TextEntry::make('instance_number')
                        ->label(__('software-online::filament/admin/resources/instance.fields.instance_number'))
                        ->prefix('#'),
                    TextEntry::make('partner.name')
                        ->label(__('software-online::filament/admin/resources/instance.fields.customer')),
                    TextEntry::make('name')
                        ->label(__('software-online::filament/admin/resources/instance.fields.name')),
                    TextEntry::make('system.name')
                        ->label(__('software-online::filament/admin/resources/instance.fields.system'))
                        ->badge(),
                    TextEntry::make('plan.name')
                        ->label(__('software-online::filament/admin/resources/instance.fields.plan'))
                        ->badge(),
                    TextEntry::make('full_url')
                        ->label(__('software-online::filament/admin/resources/instance.fields.instance_url'))
                        ->url(fn (OnlineInstance $record): string => $record->full_url)
                        ->openUrlInNewTab()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make(__('software-online::filament/admin/resources/instance.sections.subscription'))
                ->schema([
                    TextEntry::make('status')
                        ->label(__('software-online::filament/admin/resources/instance.fields.status'))
                        ->badge(),
                    TextEntry::make('billing_cycle')
                        ->label(__('software-online::filament/admin/resources/instance.fields.billing_cycle'))
                        ->badge(),
                    TextEntry::make('price')
                        ->label(__('software-online::filament/admin/resources/instance.fields.price'))
                        ->money('EGP'),
                    TextEntry::make('auto_renew')
                        ->label(__('software-online::filament/admin/resources/instance.fields.auto_renew'))
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state
                            ? __('software-online::filament/admin/resources/instance.values.enabled')
                            : __('software-online::filament/admin/resources/instance.values.disabled')),
                    TextEntry::make('starts_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.starts_at'))
                        ->dateTime(),
                    TextEntry::make('expires_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.expires_at'))
                        ->dateTime(),
                ])
                ->columns(3),
            Section::make(__('software-online::filament/admin/resources/instance.sections.remote_sync'))
                ->schema([
                    TextEntry::make('remote_tenant_id')
                        ->label(__('software-online::filament/admin/resources/instance.fields.remote_tenant_id')),
                    TextEntry::make('last_api_sync_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.last_api_sync_at'))
                        ->dateTime(),
                    TextEntry::make('last_api_error')
                        ->label(__('software-online::filament/admin/resources/instance.fields.last_api_error'))
                        ->columnSpanFull(),
                ])
                ->columns(2)
                ->collapsed(),
        ]);
    }

    public static function updateCreateUrlPreview(Get $get, Set $set): void
    {
        $system = OnlineSystem::query()->find($get('system_id'));
        $set('instance_url', $system?->tenantLoginUrl((string) $get('subdomain')));
    }

    public static function updateCreateSubscriptionPreview(Get $get, Set $set): void
    {
        $plan = OnlineSystemPlan::query()->find($get('plan_id'));
        $cycle = BillingCycle::tryFrom($get('billing_cycle') instanceof BillingCycle
            ? $get('billing_cycle')->value
            : (string) $get('billing_cycle')) ?? BillingCycle::Monthly;

        if (! $plan) {
            $set('price', 0);
            $set('expires_at', null);

            return;
        }

        $startsAt = now();
        $set('starts_at', $startsAt);
        $set('price', $plan->priceFor($cycle));
        $set('expires_at', $plan->expiresAtFor($cycle, $startsAt));
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('instance_number')
                    ->label(__('software-online::filament/admin/resources/instance.fields.instance_number'))
                    ->prefix('#')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('partner.name')
                    ->label(__('software-online::filament/admin/resources/instance.fields.customer'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('system.name')
                    ->label(__('software-online::filament/admin/resources/instance.fields.system'))
                    ->badge()
                    ->color('primary')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('software-online::filament/admin/resources/instance.fields.name'))
                    ->searchable()
                    ->description(fn (OnlineInstance $record) => $record->subdomain ? "Subdomain: {$record->subdomain}" : null),
                TextColumn::make('plan.name')
                    ->label(__('software-online::filament/admin/resources/instance.fields.plan'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label(__('software-online::filament/admin/resources/instance.fields.status'))
                    ->badge(),
                TextColumn::make('expires_at')
                    ->label(__('software-online::filament/admin/resources/instance.fields.expires_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('system_id')
                    ->label(__('software-online::filament/admin/resources/instance.fields.system'))
                    ->relationship('system', 'name'),
                SelectFilter::make('status')
                    ->label(__('software-online::filament/admin/resources/instance.fields.status'))
                    ->options(InstanceStatus::class),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('openWebsite')
                        ->label(__('software-online::filament/admin/resources/instance.actions.visit_website'))
                        ->icon('heroicon-o-arrow-top-right-on-square')
                        ->color('success')
                        ->url(fn (OnlineInstance $record) => $record->full_url)
                        ->openUrlInNewTab()
                        ->visible(fn (OnlineInstance $record) => $record->full_url !== '#'),
                    Action::make('provision')
                        ->label(__('software-online::filament/admin/resources/instance.actions.provision_api'))
                        ->icon('heroicon-o-cloud-arrow-up')
                        ->color('info')
                        ->requiresConfirmation()
                        ->action(function (OnlineInstance $record) {
                            $success = app(OnlineSystemProvisioningService::class)->provisionInstance($record);
                            if ($success) {
                                Notification::make()
                                    ->title(__('software-online::filament/admin/resources/instance.notifications.provision_success'))
                                    ->success()
                                    ->send();
                            } else {
                                Notification::make()
                                    ->title(__('software-online::filament/admin/resources/instance.notifications.provision_failed'))
                                    ->body($record->last_api_error)
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('renew')
                        ->label(__('software-online::filament/admin/resources/instance.actions.renew'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('warning')
                        ->form([
                            Select::make('billing_cycle')
                                ->label(__('software-online::filament/admin/resources/instance.fields.billing_cycle'))
                                ->options(BillingCycle::class)
                                ->default(BillingCycle::Monthly)
                                ->required(),
                        ])
                        ->action(function (OnlineInstance $record, array $data) {
                            $cycle = BillingCycle::tryFrom($data['billing_cycle']) ?? BillingCycle::Monthly;
                            try {
                                app(OnlineBillingService::class)->renewInstance($record, $cycle);
                                Notification::make()
                                    ->title(__('software-online::filament/admin/resources/instance.notifications.renew_success'))
                                    ->success()
                                    ->send();
                            } catch (\Exception $e) {
                                Notification::make()
                                    ->title(__('software-online::filament/admin/resources/instance.notifications.renew_failed'))
                                    ->body($e->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('syncStatus')
                        ->label(__('software-online::filament/admin/resources/instance.actions.sync_status'))
                        ->icon('heroicon-o-arrow-path-rounded-square')
                        ->action(fn (OnlineInstance $record) => static::runRemoteAction(
                            $record,
                            fn (OnlineSystemProvisioningService $service): bool => $service->syncStatus($record),
                            'sync_success',
                            'sync_failed',
                        ))
                        ->visible(fn (OnlineInstance $record): bool => filled($record->remote_tenant_id)),
                    Action::make('syncEntitlements')
                        ->label(__('software-online::filament/admin/resources/instance.actions.sync_entitlements'))
                        ->icon('heroicon-o-adjustments-horizontal')
                        ->requiresConfirmation()
                        ->action(fn (OnlineInstance $record) => static::runRemoteAction(
                            $record,
                            fn (OnlineSystemProvisioningService $service): bool => $service->updateEntitlements($record),
                            'entitlements_success',
                            'entitlements_failed',
                        ))
                        ->visible(fn (OnlineInstance $record): bool => filled($record->remote_tenant_id)),
                    Action::make('suspend')
                        ->label(__('software-online::filament/admin/resources/instance.actions.suspend'))
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (OnlineInstance $record) => static::runRemoteAction(
                            $record,
                            fn (OnlineSystemProvisioningService $service): bool => $service->suspendInstance($record),
                            'suspend_success',
                            'suspend_failed',
                        ))
                        ->visible(fn (OnlineInstance $record): bool => $record->status === InstanceStatus::Active && filled($record->remote_tenant_id)),
                    Action::make('activate')
                        ->label(__('software-online::filament/admin/resources/instance.actions.activate'))
                        ->icon('heroicon-o-play-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (OnlineInstance $record) => static::runRemoteAction(
                            $record,
                            fn (OnlineSystemProvisioningService $service): bool => $service->activateInstance($record),
                            'activate_success',
                            'activate_failed',
                        ))
                        ->visible(fn (OnlineInstance $record): bool => $record->status === InstanceStatus::Suspended && filled($record->remote_tenant_id)),
                    ViewAction::make(),
                    EditAction::make(),
                    static::deleteRemoteAction(),
                ]),
            ])
            ->toolbarActions([]);
    }

    public static function deleteRemoteAction(): Action
    {
        return Action::make('deleteRemote')
            ->label(__('software-online::filament/admin/resources/instance.actions.delete'))
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->requiresConfirmation()
            ->action(fn (OnlineInstance $record) => static::runRemoteAction(
                $record,
                fn (OnlineSystemProvisioningService $service): bool => $service->deleteInstance($record),
                'delete_started',
                'delete_failed',
            ))
            ->visible(fn (OnlineInstance $record): bool => filled($record->remote_tenant_id)
                && ! in_array($record->status, [InstanceStatus::Deleting, InstanceStatus::Deleted], true));
    }

    private static function runRemoteAction(
        OnlineInstance $record,
        \Closure $callback,
        string $successMessage,
        string $failureMessage,
    ): void {
        $success = $callback(app(OnlineSystemProvisioningService::class));
        $record->refresh();

        $notification = Notification::make()
            ->title(__('software-online::filament/admin/resources/instance.notifications.'.($success ? $successMessage : $failureMessage)))
            ->body($success ? null : $record->last_api_error);

        if ($success) {
            $notification->success();
        } else {
            $notification->danger();
        }

        $notification->send();
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListOnlineInstances::route('/'),
            'create' => CreateOnlineInstance::route('/create'),
            'view'   => ViewOnlineInstance::route('/{record}'),
            'edit'   => EditOnlineInstance::route('/{record}/edit'),
        ];
    }
}
