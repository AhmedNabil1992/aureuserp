<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\Action;
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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\CreateLicense;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\ListLicenses;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\ViewLicense;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers\DevicesRelationManager;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers\SubscriptionsRelationManager;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseInvoice;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Support\Models\City;

class LicenseResource extends Resource
{
    protected static ?string $model = License::class;

    protected static ?string $slug = 'licenses';

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-key';

    protected static ?string $cluster = Licensing::class;

    public static function getNavigationGroup(): string
    {
        return 'Software';
    }

    public static function getNavigationLabel(): string
    {
        return 'Licenses';
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
            ->columns([
                TextColumn::make('serial_number')->searchable()->sortable(),
                TextColumn::make('program.slug')->label('Program')->searchable(),
                TextColumn::make('edition.name')->label('Edition')->searchable(),
                TextColumn::make('partner.name')->label('Partner')->searchable(),
                TextColumn::make('partner.phone')->label('Partner Phone')->searchable(),
                TextColumn::make('state.name_ar')->label('State')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('city.name_ar')->label('City')->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('license_plan')->badge(),
                TextColumn::make('period')->numeric()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('start_date')->date()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('end_date')->date()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('status')->badge(),
                IconColumn::make('is_active')->boolean(),
                TextColumn::make('requested_at')->dateTime()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('approver.name')->label('Approver')->searchable()->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')->dateTime()->sortable(),
            ])
            ->recordUrl(fn (License $record): string => static::getUrl('view', ['record' => $record]))
            ->recordActions([
                Action::make('billLicense')
                    ->label('Bill License')
                    ->icon('heroicon-o-receipt-percent')
                    ->color('success')
                    ->form([
                        Select::make('edition_id')
                            ->label('Edition')
                            ->options(fn (License $record): array => ProgramEdition::query()
                                ->where('program_id', $record->program_id)
                                ->orderBy('name')
                                ->pluck('name', 'id')
                                ->all())
                            ->default(fn (License $record): ?int => $record->edition_id)
                            ->required()
                            ->searchable(),
                        Select::make('license_plan')
                            ->label('Type')
                            ->options(collect(LicensePlan::cases())->mapWithKeys(fn (LicensePlan $case): array => [
                                $case->value => ucfirst($case->value),
                            ])->all())
                            ->default(fn (License $record): string => $record->license_plan?->value ?? LicensePlan::Full->value)
                            ->required(),
                    ])
                    ->action(function (License $record, array $data): void {
                        try {
                            $invoiceNumber = DB::transaction(function () use ($record, $data): string {
                                $edition = ProgramEdition::query()
                                    ->where('program_id', $record->program_id)
                                    ->findOrFail((int) $data['edition_id']);

                                $plan = LicensePlan::from((string) $data['license_plan']);

                                $amount = match ($plan) {
                                    LicensePlan::Full    => (float) ($edition->license_price ?? 0),
                                    LicensePlan::Monthly => (float) ($edition->monthly_renewal ?? 0),
                                    LicensePlan::Annual  => (float) ($edition->annual_renewal ?? 0),
                                };

                                if ($amount <= 0) {
                                    throw new \RuntimeException('No price configured for this Edition and Type.');
                                }

                                $record->update([
                                    'edition_id'    => $edition->id,
                                    'license_plan'  => $plan->value,
                                    'period'        => match ($plan) {
                                        LicensePlan::Full    => 0,
                                        LicensePlan::Monthly => 30,
                                        LicensePlan::Annual  => 365,
                                    },
                                    'start_date'    => now()->toDateString(),
                                    'end_date'      => match ($plan) {
                                        LicensePlan::Full    => null,
                                        LicensePlan::Monthly => now()->addMonth()->toDateString(),
                                        LicensePlan::Annual  => now()->addYear()->toDateString(),
                                    },
                                    'status'        => LicenseStatus::Approved->value,
                                    'is_active'     => true,
                                    'approved_by'   => Auth::id(),
                                ]);

                                $invoiceNumber = 'LIC-'.now()->format('YmdHis').'-'.Str::upper(Str::random(4));

                                LicenseInvoice::query()->create([
                                    'license_id'     => $record->id,
                                    'program_id'     => $record->program_id,
                                    'edition_id'     => $edition->id,
                                    'license_plan'   => $plan->value,
                                    'invoice_number' => $invoiceNumber,
                                    'item_name'      => ($record->program?->name ?? 'Software License').' - '.$edition->name,
                                    'quantity'       => 1,
                                    'unit_price'     => $amount,
                                    'amount'         => $amount,
                                    'billed_by'      => Auth::id(),
                                    'billed_at'      => now(),
                                    'notes'          => 'Generated from LicenseResource billing action.',
                                ]);

                                return $invoiceNumber;
                            });

                            Notification::make()
                                ->title('Invoice created successfully')
                                ->body('Invoice No: '.$invoiceNumber)
                                ->success()
                                ->send();
                        } catch (\Throwable $exception) {
                            Notification::make()
                                ->title('Failed to create invoice')
                                ->body($exception->getMessage())
                                ->danger()
                                ->send();
                        }
                    }),
                DeleteAction::make(),
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
