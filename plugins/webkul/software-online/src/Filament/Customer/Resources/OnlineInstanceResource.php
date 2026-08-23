<?php

namespace Webkul\SoftwareOnline\Filament\Customer\Resources;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
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

class OnlineInstanceResource extends Resource
{
    protected static ?string $model = OnlineInstance::class;

    protected static ?string $slug = 'my-online-websites';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-globe-alt';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::SoftwareOnline;
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
                    ->description(fn (OnlineInstance $record) => $record->subdomain ? "{$record->subdomain}" : null),
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
                    ->visible(fn (OnlineInstance $record) => ! empty($record->full_url)),
                Action::make('renew')
                    ->label(__('software-online::filament/customer/resources/my_instances.actions.renew'))
                    ->icon('heroicon-o-arrow-path')
                    ->color('warning')
                    ->form([
                        Select::make('billing_cycle')
                            ->label(__('software-online::filament/customer/resources/my_instances.fields.billing_cycle'))
                            ->options(BillingCycle::class)
                            ->default(BillingCycle::Monthly)
                            ->required(),
                    ])
                    ->action(function (OnlineInstance $record, array $data) {
                        $cycle = BillingCycle::tryFrom($data['billing_cycle']) ?? BillingCycle::Monthly;
                        try {
                            app(OnlineBillingService::class)->renewInstance($record, $cycle);
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
