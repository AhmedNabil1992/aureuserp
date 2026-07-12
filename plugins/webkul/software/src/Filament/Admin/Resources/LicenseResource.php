<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\CreateLicense;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\ListLicenses;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\ViewLicense;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers\DevicesRelationManager;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers\SubscriptionsRelationManager;
use Webkul\Software\Models\License;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Services\LicenseManager;
use Webkul\Support\Models\City;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $slug = 'licenses';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $cluster = Licensing::class;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/license.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('serial_number')->required()->maxLength(50),
                Select::make('program_id')->relationship('program', 'name')->searchable()->preload()->required(),
                Select::make('edition_id')->relationship('edition', 'name')->searchable()->preload()->required(),
                Select::make('partner_id')->relationship('partner', 'name')->searchable()->preload()->required(),
                Select::make('state_id')
                    ->relationship('state', 'name')
                    ->searchable()
                    ->preload()
                    ->live()
                    ->afterStateUpdated(fn (Set $set): mixed => $set('city_id', null)),
                Select::make('city_id')
                    ->options(function (Get $get): array {
                        $stateId = $get('state_id');

                        if (! $stateId) {
                            return [];
                        }

                        return City::query()
                            ->where('state_id', $stateId)
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all();
                    })
                    ->searchable()
                    ->preload()
                    ->disabled(fn (Get $get): bool => ! $get('state_id')),
                Select::make('license_plan')
                    ->options(collect(LicensePlan::cases())->mapWithKeys(fn (LicensePlan $case): array => [$case->value => ucfirst($case->value)])->all())
                    ->required(),
                Select::make('status')
                    ->options(collect(LicenseStatus::cases())->mapWithKeys(fn (LicenseStatus $case): array => [$case->value => ucfirst($case->value)])->all())
                    ->default(LicenseStatus::Pending->value)
                    ->required(),
                TextInput::make('period')->numeric()->minValue(1),
                DatePicker::make('start_date')->native(false),
                DatePicker::make('end_date')->native(false),
                DateTimePicker::make('requested_at'),
                TextInput::make('request_source')->maxLength(50),
                TextInput::make('company_name')->maxLength(255),
                TextInput::make('server_ip')->maxLength(45),
                Toggle::make('is_active')->default(true),
            ])
            ->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->with('latestActivity'))
            ->columns([
                TextColumn::make('serial_number')->searchable()->sortable()->badge()->copyable(),
                TextColumn::make('program.slug')->label(__('software::filament/admin/resources/license.table.columns.program'))->searchable(),
                TextColumn::make('edition.name')->label(__('software::filament/admin/resources/license.table.columns.edition'))->searchable(),
                TextColumn::make('partner.name')->label(__('software::filament/admin/resources/license.table.columns.partner'))->searchable(),
                TextColumn::make('partner.phone')->label(__('software::filament/admin/resources/license.table.columns.partner_phone'))->searchable(),
                TextColumn::make('current_client_version')
                    ->label('Current Version')
                    ->state(fn (License $record): ?string => $record->currentClientVersion())
                    ->placeholder('-')
                    ->searchable(false),
                TextColumn::make('last_client_online_at')
                    ->label('Last Online At')
                    ->state(fn (License $record) => $record->lastClientOnlineAt())
                    ->dateTime()
                    ->placeholder('-'),
                TextColumn::make('state.name')->label(__('software::filament/admin/resources/license.table.columns.state'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city.name')->label(__('software::filament/admin/resources/license.table.columns.city'))->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('license_plan')->badge(),
                TextColumn::make('period')->numeric()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')->date()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')->date()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('requested_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approver.name')->label(__('software::filament/admin/resources/license.table.columns.approver'))->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->recordUrl(fn (License $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                ActionGroup::make([
                    Action::make('billLicense')
                        ->label(__('software::filament/admin/resources/license.actions.bill_license'))
                        ->icon('heroicon-o-receipt-percent')
                        ->color('success')
                        ->visible(fn (License $record): bool => ! $record->invoices()->exists() && ! $record->devices()->whereNotNull('license_key')->exists())
                        ->form([
                            Select::make('edition_id')
                                ->label(__('software::filament/admin/resources/license.actions.edition'))
                                ->options(fn (License $record): array => ProgramEdition::query()
                                    ->where('program_id', $record->program_id)
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->default(fn (License $record): ?int => $record->edition_id)
                                ->required()
                                ->searchable(),
                            Select::make('license_plan')
                                ->label(__('software::filament/admin/resources/license.actions.type'))
                                ->options(collect(LicensePlan::cases())->mapWithKeys(fn (LicensePlan $case): array => [
                                    $case->value => ucfirst($case->value),
                                ])->all())
                                ->default(fn (License $record): string => $record->license_plan?->value ?? LicensePlan::Full->value)
                                ->live()
                                ->required(),
                        ])
                        ->action(function (License $record, array $data): void {
                            try {
                                $result = app(LicenseManager::class)->billLicense(
                                    $record,
                                    (int) $data['edition_id'],
                                    (string) $data['license_plan']
                                );

                                if ($result['isTrial']) {
                                    Notification::make()
                                        ->title(__('software::filament/admin/resources/license.notifications.trial_activated'))
                                        ->body(__('software::filament/admin/resources/license.notifications.trial_expires_on', ['date' => $result['license']->end_date?->format('Y-m-d')]))
                                        ->success()
                                        ->send();

                                    return;
                                }

                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.invoice_created'))
                                    ->body(__('software::filament/admin/resources/license.notifications.invoice_number', ['number' => $result['invoiceNumber']]))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.invoice_failed'))
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('renewLicense')
                        ->label(__('software::filament/admin/resources/license.actions.renew'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('info')
                        ->visible(fn (License $record): bool => $record->invoices()->exists()
                            && filled($record->edition_id)
                            && in_array($record->license_plan?->value, [LicensePlan::Monthly->value, LicensePlan::Annual->value], true))
                        ->form([
                            Select::make('license_plan')
                                ->label(__('software::filament/admin/resources/license.actions.type'))
                                ->options([
                                    LicensePlan::Monthly->value => ucfirst(LicensePlan::Monthly->value),
                                    LicensePlan::Annual->value  => ucfirst(LicensePlan::Annual->value),
                                ])
                                ->default(fn (License $record): string => in_array($record->license_plan?->value, [LicensePlan::Monthly->value, LicensePlan::Annual->value], true)
                                    ? $record->license_plan->value
                                    : LicensePlan::Annual->value)
                                ->required(),
                        ])
                        ->requiresConfirmation()
                        ->action(function (License $record, array $data): void {
                            try {
                                $result = app(LicenseManager::class)->renewLicense(
                                    $record,
                                    (string) $data['license_plan']
                                );

                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.renew_success'))
                                    ->body(__('software::filament/admin/resources/license.notifications.invoice_number', ['number' => $result['invoice']->invoice_number]))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.renew_failed'))
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('activateLicense')
                        ->label(__('software::filament/admin/resources/license.actions.activate'))
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->visible(fn (License $record): bool => app(LicenseManager::class)->canActivateLicense($record))
                        ->requiresConfirmation()
                        ->action(function (License $record): void {
                            try {
                                app(LicenseManager::class)->activateLicense($record);

                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.activate_success'))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.activate_failed'))
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('deactivateLicense')
                        ->label(__('software::filament/admin/resources/license.actions.deactivate'))
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->visible(fn (License $record): bool => $record->is_active)
                        ->requiresConfirmation()
                        ->action(function (License $record): void {
                            try {
                                app(LicenseManager::class)->deactivateLicense($record);

                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.deactivate_success'))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.deactivate_failed'))
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('expireLicense')
                        ->label(__('software::filament/admin/resources/license.actions.expire'))
                        ->icon('heroicon-o-no-symbol')
                        ->color('danger')
                        ->visible(fn (License $record): bool => $record->is_active
                            && in_array($record->license_plan?->value, [LicensePlan::Monthly->value, LicensePlan::Annual->value], true))
                        ->requiresConfirmation()
                        ->action(function (License $record): void {
                            try {
                                app(LicenseManager::class)->expireLicense($record);

                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.expire_success'))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Notification::make()
                                    ->title(__('software::filament/admin/resources/license.notifications.expire_failed'))
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    DeleteAction::make(),
                ]),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionsRelationManager::class,
            DevicesRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListLicenses::route('/'),
            'create' => CreateLicense::route('/create'),
            'view'   => ViewLicense::route('/{record}'),
        ];
    }
}
