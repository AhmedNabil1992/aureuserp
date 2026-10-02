<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseSubscriptionResource\Pages\ManageLicenseSubscriptions;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseSubscription;
use Webkul\Software\Models\ProgramFeature;
use Webkul\Software\Services\LicenseSubscriptionBillingService;

class LicenseSubscriptionResource extends Resource
{
    protected static ?string $model = LicenseSubscription::class;

    protected static ?string $slug = 'license-subscriptions';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-calendar-days';

    protected static ?int $navigationSort = 2;

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/license-subscription.navigation.label');
    }

    public static function getModelLabel(): string
    {
        return __('software::filament/admin/resources/license-subscription.navigation.label');
    }

    protected static ?string $cluster = Licensing::class;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('license_id')->label(__('software::filament/admin/resources/license-subscription.form.fields.license'))->relationship('license', 'serial_number')->searchable()->preload()->live()->required(),
            Select::make('feature_id')->label(__('software::filament/admin/resources/license-subscription.form.fields.service_type'))
                ->options(function (Get $get): array {
                    $license = License::find($get('license_id'));

                    return $license
                        ? ProgramFeature::query()->where('program_id', $license->program_id)->whereNotNull('service_type')->pluck('name', 'id')->all()
                        : [];
                })
                ->searchable()
                ->preload()
                ->required(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('license.serial_number')->label(__('software::filament/admin/resources/license-subscription.table.columns.license'))->searchable(),
            TextColumn::make('service_type')->label(__('software::filament/admin/resources/license-subscription.table.columns.service_type'))->badge(),
            TextColumn::make('start_date')->label(__('software::filament/admin/resources/license-subscription.table.columns.start_date'))->date(),
            TextColumn::make('end_date')->label(__('software::filament/admin/resources/license-subscription.table.columns.end_date'))->date(),
            IconColumn::make('is_active')->label(__('software::filament/admin/resources/license-subscription.table.columns.is_active'))->boolean(),
        ])->recordActions([
            Action::make('renew')
                ->label(__('software::filament/admin/resources/license-subscription.actions.renew'))
                ->icon('heroicon-o-arrow-path')
                ->requiresConfirmation()
                ->action(function (LicenseSubscription $record): void {
                    $featureId = $record->feature_id ?: ProgramFeature::query()
                        ->where('program_id', $record->license->program_id)
                        ->where('service_type', $record->service_type)
                        ->value('id');

                    if (! $featureId) {
                        throw new \RuntimeException('No matching program feature was found for this subscription.');
                    }

                    app(LicenseSubscriptionBillingService::class)
                        ->subscribeOrRenew($record->license, (int) $featureId, false);
                }),
            DeleteAction::make(),
        ])->toolbarActions([
            // DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageLicenseSubscriptions::route('/'),
        ];
    }
}
