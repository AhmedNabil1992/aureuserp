<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\KeyValue;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
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
                        ->searchable(),
                    TextInput::make('name')
                        ->label(__('software-online::filament/admin/resources/instance.fields.name'))
                        ->required(),
                    TextInput::make('subdomain')
                        ->label(__('software-online::filament/admin/resources/instance.fields.subdomain'))
                        ->placeholder('my-store')
                        ->required()
                        ->maxLength(50)
                        ->dehydrateStateUsing(fn (mixed $state): string => strtolower(trim((string) $state)))
                        ->rules([new Subdomain])
                        ->unique(
                            ignoreRecord: true,
                            modifyRuleUsing: fn (Unique $rule, Get $get): Unique => $rule->where('system_id', $get('system_id')),
                        ),
                    TextInput::make('custom_domain')
                        ->label(__('software-online::filament/admin/resources/instance.fields.custom_domain'))
                        ->placeholder('store.example.com'),
                    TextInput::make('instance_url')
                        ->label(__('software-online::filament/admin/resources/instance.fields.instance_url'))
                        ->placeholder('https://my-store.poscloud.com')
                        ->columnSpanFull(),
                ])->columns(3),

            Section::make(__('software-online::filament/admin/resources/instance.sections.subscription'))
                ->schema([
                    Select::make('status')
                        ->label(__('software-online::filament/admin/resources/instance.fields.status'))
                        ->options(InstanceStatus::class)
                        ->default(InstanceStatus::Active)
                        ->required(),
                    Select::make('billing_cycle')
                        ->label(__('software-online::filament/admin/resources/instance.fields.billing_cycle'))
                        ->options(BillingCycle::class)
                        ->default(BillingCycle::Monthly)
                        ->required(),
                    TextInput::make('price')
                        ->label(__('software-online::filament/admin/resources/instance.fields.price'))
                        ->numeric()
                        ->prefix('EGP')
                        ->default(0.00),
                    DateTimePicker::make('starts_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.starts_at'))
                        ->default(now())
                        ->required(),
                    DateTimePicker::make('expires_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.expires_at'))
                        ->required()
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
                ])->columns(2)->collapsed(),
        ]);
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
                Action::make('openWebsite')
                    ->label(__('software-online::filament/admin/resources/instance.actions.visit_website'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (OnlineInstance $record) => $record->full_url)
                    ->openUrlInNewTab()
                    ->visible(fn (OnlineInstance $record) => ! empty($record->full_url)),
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
