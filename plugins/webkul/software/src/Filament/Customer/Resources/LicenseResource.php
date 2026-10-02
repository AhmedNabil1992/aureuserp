<?php

namespace Webkul\Software\Filament\Customer\Resources;

use Closure;
use Filament\Actions\Action;
use Filament\Actions\ActionGroup;
use Filament\Actions\ViewAction;
use Filament\Facades\Filament;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\TextInputColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Filament\Customer\Resources\LicenseResource\Pages\ListLicenses;
use Webkul\Software\Filament\Customer\Resources\LicenseResource\Pages\ViewLicense;
use Webkul\Software\Filament\Customer\Resources\LicenseResource\RelationManagers\SubscriptionsRelationManager;
use Webkul\Software\Models\License;
use Webkul\Software\Models\ProgramFeature;
use Webkul\Software\Services\EmailValidationService;
use Webkul\Software\Services\LicenseSubscriptionBillingService;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $slug = 'licenses';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return __('software::filament/customer/license.navigation.label');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function getModelLabel(): string
    {
        return __('software::filament/customer/license.models.singular');
    }

    public static function getPluralModelLabel(): string
    {
        return __('software::filament/customer/license.models.plural');
    }

    public static function canAccess(): bool
    {
        $user = Filament::auth()->user();

        if (! $user) {
            return false;
        }

        if (! Schema::hasTable('software_licenses')) {
            return false;
        }

        return License::where('partner_id', $user->id)->exists();
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->where('partner_id', Auth::guard('customer')->id());
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                // TextColumn::make('serial_number')
                //     ->label(__('software::filament/customer/license.table.columns.serial_number'))
                //     ->searchable()
                //     ->sortable()
                //     ->copyable(),

                TextColumn::make('program.name')
                    ->label(__('software::filament/customer/license.table.columns.program_name')),
                TextInputColumn::make('company_name')
                    ->label(__('software::filament/customer/license.table.columns.company_name'))
                    ->rules(['required', 'string', 'max:255']),
                TextColumn::make('edition.name')
                    ->label(__('software::filament/customer/license.table.columns.edition')),
                TextColumn::make('state.name')
                    ->label(__('software::filament/customer/license.table.columns.state')),
                TextColumn::make('city.name')
                    ->label(__('software::filament/customer/license.table.columns.city')),
                TextInputColumn::make('address')
                    ->label(__('software::filament/customer/license.table.columns.address'))
                    ->rules(['required', 'string', 'max:255']),
                TextColumn::make('license_plan')
                    ->label(__('software::filament/customer/license.table.columns.license_plan'))
                    ->badge()
                    ->formatStateUsing(fn ($state) => $state instanceof LicensePlan ? ucfirst($state->value) : (is_string($state) ? ucfirst($state) : '—')),
                TextColumn::make('status')
                    ->label(__('software::filament/customer/license.table.columns.status'))
                    ->badge(),
                TextColumn::make('start_date')
                    ->label(__('software::filament/customer/license.table.columns.start_date'))
                    ->date('Y-m-d'),
                TextColumn::make('end_date')
                    ->label(__('software::filament/customer/license.table.columns.end_date'))
                    ->date('Y-m-d')
                    ->color(fn ($record) => $record->end_date < now() ? 'danger' : 'success'),
                TextColumn::make('current_client_version')
                    ->label(__('software::filament/customer/license.table.columns.version'))
                    ->state(fn (License $record): ?string => $record->currentClientVersion())
                    ->placeholder('-')
                    ->searchable(false),
                TextColumn::make('devices_count')
                    ->label(__('software::filament/customer/license.table.columns.devices_count'))
                    ->counts('devices'),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label(__('software::filament/customer/license.table.filters.status'))
                    ->options([
                        'active'    => __('software::filament/customer/license.statuses.active'),
                        'inactive'  => __('software::filament/customer/license.statuses.inactive'),
                        'suspended' => __('software::filament/customer/license.statuses.suspended'),
                        'expired'   => __('software::filament/customer/license.statuses.expired'),
                    ])
                    ->multiple(),

                SelectFilter::make('program_id')
                    ->label(__('software::filament/customer/license.table.filters.program'))
                    ->relationship('program', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ActionGroup::make([
                    Action::make('renew_service')
                        ->label(__('software::filament/customer/license.table.actions.renew_service.label'))
                        ->icon('heroicon-o-arrow-path')
                        ->color('success')
                        ->form([
                            Select::make('feature_id')
                                ->label(__('software::filament/customer/license.table.actions.renew_service.service'))
                                ->options(fn (License $record): array => ProgramFeature::query()
                                    ->where('program_id', $record->program_id)
                                    ->whereNotNull('service_type')
                                    ->whereNotNull('product_id')
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->required()
                                ->searchable()
                                ->preload(),
                        ])
                        ->requiresConfirmation()
                        ->action(function (License $record, array $data): void {
                            try {
                                $result = app(LicenseSubscriptionBillingService::class)
                                    ->subscribeOrRenew($record, (int) $data['feature_id'], true);

                                Notification::make()
                                    ->title(__('software::filament/customer/license.table.actions.renew_service.notifications.success'))
                                    ->body(__('software::filament/customer/license.table.actions.renew_service.notifications.ends_on', [
                                        'date' => $result['subscription']->end_date?->format('Y-m-d'),
                                    ]))
                                    ->success()
                                    ->send();
                            } catch (\Throwable $exception) {
                                Notification::make()
                                    ->title(__('software::filament/customer/license.table.actions.renew_service.notifications.failed'))
                                    ->body($exception->getMessage())
                                    ->danger()
                                    ->send();
                            }
                        }),
                    Action::make('shift_emails')
                        ->label(__('software::filament/customer/license.table.actions.shift_emails.label'))
                        ->icon('heroicon-o-envelope')
                        ->modalHeading(__('software::filament/customer/license.table.actions.shift_emails.modal.heading'))
                        ->modalDescription(__('software::filament/customer/license.table.actions.shift_emails.modal.description'))
                        ->modalSubmitActionLabel(__('software::filament/customer/license.table.actions.shift_emails.modal.submit'))
                        ->fillForm(fn (License $record): array => [
                            'emails' => $record->shiftEmails()
                                ->orderBy('id')
                                ->get(['email'])
                                ->map(fn ($shiftEmail): array => ['email' => $shiftEmail->email])
                                ->all(),
                        ])
                        ->form([
                            Repeater::make('emails')
                                ->label(__('software::filament/customer/license.table.actions.shift_emails.form.emails.label'))
                                ->addActionLabel(__('software::filament/customer/license.table.actions.shift_emails.form.emails.add'))
                                ->schema([
                                    TextInput::make('email')
                                        ->label(__('software::filament/customer/license.table.actions.shift_emails.form.email.label'))
                                        ->email()
                                        ->required()
                                        ->maxLength(255)
                                        ->rules([
                                            fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                                                $validation = EmailValidationService::validate((string) $value);

                                                if (! $validation['valid']) {
                                                    $fail($validation['message']);
                                                }
                                            },
                                        ]),
                                ])
                                ->columns(1),
                        ])
                        ->action(function (array $data, License $record): void {
                            $emails = collect($data['emails'] ?? [])
                                ->pluck('email')
                                ->map(fn (string $email): string => strtolower(trim($email)))
                                ->filter()
                                ->unique()
                                ->values();

                            DB::transaction(function () use ($emails, $record): void {
                                $record->shiftEmails()->delete();

                                $record->shiftEmails()->createMany(
                                    $emails->map(fn (string $email): array => ['email' => $email])->all(),
                                );
                            });

                            Notification::make()
                                ->title(__('software::filament/customer/license.table.actions.shift_emails.notifications.saved.title'))
                                ->success()
                                ->send();
                        }),
                    ViewAction::make(),
                ]),
            ])
            ->paginated([10, 25, 50])
            ->defaultSort('start_date', 'desc')
            ->modifyQueryUsing(fn (Builder $query): Builder => $query->orderByDesc('created_at'));
    }

    public static function getRelations(): array
    {
        return [
            SubscriptionsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListLicenses::route('/'),
            'view'  => ViewLicense::route('/{record}'),
        ];
    }
}
