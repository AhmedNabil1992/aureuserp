<?php

namespace Webkul\SoftwareOnline\Filament\Customer\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource\Pages\ListOnlineInstances;
use Webkul\SoftwareOnline\Filament\Customer\Resources\OnlineInstanceResource\Pages\ViewOnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Services\OnlineBillingService;
use Webkul\Support\Enums\NavigationGroup;

class OnlineInstanceResource extends Resource
{
    protected static ?string $model = OnlineInstance::class;

    protected static ?string $slug = 'my-online-websites';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/customer/resources/my_instances.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('software-online::filament/customer/resources/my_instances.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software-online::filament/customer/resources/my_instances.models.plural');
    }

    public static function getEloquentQuery(): Builder
    {
        $partnerId = Auth::guard('customer')->id();

        return parent::getEloquentQuery()
            ->where('partner_id', $partnerId);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('software-online::filament/customer/resources/my_instances.sections.website'))
                ->schema([
                    TextEntry::make('name')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.name')),
                    TextEntry::make('system.name')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.system'))
                        ->badge(),
                    TextEntry::make('plan.name')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.plan'))
                        ->badge(),
                    TextEntry::make('status')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.status'))
                        ->badge(),
                    TextEntry::make('full_url')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.url'))
                        ->url(fn (OnlineInstance $record): ?string => $record->status === InstanceStatus::Active
                            ? $record->full_url
                            : null)
                        ->openUrlInNewTab()
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make(__('software-online::filament/customer/resources/my_instances.sections.subscription'))
                ->schema([
                    TextEntry::make('billing_cycle')
                        ->label(__('software-online::filament/customer/resources/my_instances.fields.billing_cycle'))
                        ->badge(),
                    TextEntry::make('price')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.price'))
                        ->money('EGP'),
                    TextEntry::make('expires_at')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.expires_at'))
                        ->dateTime(),
                    TextEntry::make('auto_renew')
                        ->label(__('software-online::filament/customer/resources/my_instances.columns.auto_renew'))
                        ->badge()
                        ->formatStateUsing(fn (bool $state): string => $state
                            ? __('software-online::filament/customer/resources/my_instances.values.enabled')
                            : __('software-online::filament/customer/resources/my_instances.values.disabled')),
                ])
                ->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('instance_number')
                    ->label(__('software-online::filament/customer/resources/my_instances.columns.number'))
                    ->prefix('#')
                    ->sortable(),
                TextColumn::make('name')
                    ->label(__('software-online::filament/customer/resources/my_instances.columns.name'))
                    ->searchable()
                    ->description(fn (OnlineInstance $record) => $record->full_url !== '#' ? $record->full_url : null),
                TextColumn::make('system.name')
                    ->label(__('software-online::filament/customer/resources/my_instances.columns.system'))
                    ->badge()
                    ->color('primary'),
                TextColumn::make('plan.name')
                    ->label(__('software-online::filament/customer/resources/my_instances.columns.plan'))
                    ->badge()
                    ->color('gray'),
                TextColumn::make('status')
                    ->label(__('software-online::filament/customer/resources/my_instances.columns.status'))
                    ->badge(),
                TextColumn::make('expires_at')
                    ->label(__('software-online::filament/customer/resources/my_instances.columns.expires_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->recordActions([
                Action::make('visit')
                    ->label(__('software-online::filament/customer/resources/my_instances.actions.visit'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->color('success')
                    ->url(fn (OnlineInstance $record) => $record->full_url)
                    ->openUrlInNewTab()
                    ->visible(fn (OnlineInstance $record) => $record->status === InstanceStatus::Active && $record->full_url !== '#'),
                Action::make('renew')
                    ->label(__('software-online::filament/customer/resources/my_instances.actions.renew'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('billing_cycle')
                            ->label(__('software-online::filament/customer/resources/my_instances.fields.billing_cycle'))
                            ->options([
                                BillingCycle::Monthly->value => BillingCycle::Monthly->getLabel(),
                                BillingCycle::Annual->value  => BillingCycle::Annual->getLabel(),
                            ])
                            ->default(fn (OnlineInstance $record) => $record->billing_cycle === BillingCycle::Annual ? BillingCycle::Annual->value : BillingCycle::Monthly->value)
                            ->required(),
                        TextInput::make('periods')
                            ->label(__('software-online::filament/customer/resources/my_instances.fields.periods'))
                            ->integer()
                            ->minValue(1)
                            ->maxValue(120)
                            ->default(1)
                            ->required(),
                    ])
                    ->action(function (OnlineInstance $record, array $data) {
                        $cycle = BillingCycle::tryFrom($data['billing_cycle']) ?? BillingCycle::Monthly;
                        try {
                            app(OnlineBillingService::class)->renewInstance(
                                instance: $record,
                                cycle: $cycle,
                                periods: (int) ($data['periods'] ?? 1),
                            );
                            Notification::make()
                                ->title(__('software-online::filament/customer/resources/my_instances.notifications.renewed_success'))
                                ->success()
                                ->send();
                        } catch (\Exception $e) {
                            Notification::make()
                                ->title(__('software-online::filament/customer/resources/my_instances.notifications.renew_failed'))
                                ->body($e->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListOnlineInstances::route('/'),
            'view'  => ViewOnlineInstance::route('/{record}'),
        ];
    }
}
