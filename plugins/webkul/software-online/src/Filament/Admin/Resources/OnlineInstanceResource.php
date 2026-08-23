<?php

namespace Webkul\SoftwareOnline\Filament\Admin\Resources;

use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
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
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Webkul\Partner\Models\Partner;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\CreateOnlineInstance;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\EditOnlineInstance;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\ListOnlineInstances;
use Webkul\SoftwareOnline\Filament\Admin\Resources\OnlineInstanceResource\Pages\ViewOnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\SoftwareOnline\Services\OnlineBillingService;
use Webkul\SoftwareOnline\Services\OnlineSystemProvisioningService;

class OnlineInstanceResource extends Resource
{
    protected static ?string $model = OnlineInstance::class;

    protected static ?string $slug = 'online-instances';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-computer-desktop';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::SoftwareOnline;
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
                        ->placeholder('my-store'),
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
                        ->default(now()),
                    DateTimePicker::make('expires_at')
                        ->label(__('software-online::filament/admin/resources/instance.fields.expires_at')),
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
                ViewAction::make(),
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
            'index'  => ListOnlineInstances::route('/'),
            'create' => CreateOnlineInstance::route('/create'),
            'view'   => ViewOnlineInstance::route('/{record}'),
            'edit'   => EditOnlineInstance::route('/{record}/edit'),
        ];
    }
}
