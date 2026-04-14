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
use Webkul\Account\Enums\DisplayType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\MoveState;
use Webkul\Account\Enums\MoveType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Move as AccountMove;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\Product;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Filament\Admin\Clusters\Licensing;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\CreateLicense;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\ListLicenses;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\Pages\ViewLicense;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers\DevicesRelationManager;
use Webkul\Software\Filament\Admin\Resources\LicenseResource\RelationManagers\SubscriptionsRelationManager;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseInvoice;
use Webkul\Software\Models\LicenseSubscription;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Models\ProgramFeature;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Company;

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
                    ->visible(fn (License $record): bool => ! $record->invoices()->exists() && ! $record->devices()->whereNotNull('license_key')->exists())
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
                            $user = Auth::user();

                            $company = $user?->default_company_id
                                ? Company::find($user->default_company_id)
                                : null;

                            if (! $company || ! $company->currency_id) {
                                throw new \RuntimeException('No default company or currency configured for the current user.');
                            }

                            $result = DB::transaction(function () use ($record, $data, $company): array {
                                $edition = ProgramEdition::query()
                                    ->where('program_id', $record->program_id)
                                    ->with(['program.product', 'variantProduct', 'product'])
                                    ->findOrFail((int) $data['edition_id']);

                                $plan = LicensePlan::from((string) $data['license_plan']);

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

                                $baseProduct = $edition->program?->product;

                                if (! $baseProduct) {
                                    throw new \RuntimeException('Program must be linked to a base service product first.');
                                }

                                $variantProduct = $edition->variantProduct;

                                if (! $variantProduct && $edition->product_id) {
                                    $legacyProduct = Product::query()->find($edition->product_id);

                                    if ($legacyProduct?->parent_id) {
                                        $variantProduct = $legacyProduct;
                                    }
                                }

                                if (! $variantProduct) {
                                    throw new \RuntimeException('Edition must be linked to a variant product.');
                                }

                                if ((int) $variantProduct->parent_id !== (int) $baseProduct->id) {
                                    throw new \RuntimeException('Linked variant does not belong to the selected program base product.');
                                }

                                if (($variantProduct->type?->value ?? $variantProduct->type) !== ProductType::SERVICE->value) {
                                    throw new \RuntimeException('Edition variant product must be of type service.');
                                }

                                $amount = (float) ($variantProduct->price ?? 0);

                                if ($amount <= 0) {
                                    throw new \RuntimeException('Selected variant price must be greater than zero.');
                                }

                                $journal = Journal::query()
                                    ->where('type', JournalType::SALE)
                                    ->where('company_id', $company->id)
                                    ->first();

                                if (! $journal) {
                                    throw new \RuntimeException('No Sales Journal configured for your default company.');
                                }

                                $itemName = $variantProduct->name;

                                // Create accounting invoice (account move)
                                $accountMove = AccountMove::create([
                                    'move_type'       => MoveType::OUT_INVOICE,
                                    'state'           => MoveState::DRAFT,
                                    'journal_id'      => $journal->id,
                                    'invoice_origin'  => $record->serial_number,
                                    'date'            => now()->toDateString(),
                                    'invoice_date'    => now()->toDateString(),
                                    'invoice_date_due'=> now()->toDateString(),
                                    'company_id'      => $company->id,
                                    'currency_id'     => $company->currency_id,
                                    'partner_id'      => $record->partner_id,
                                    'creator_id'      => Auth::id(),
                                    'invoice_user_id' => Auth::id(),
                                ]);

                                $accountMove->invoiceLines()->create([
                                    'name'         => $itemName.' ('.ucfirst($plan->value).')',
                                    'date'         => $accountMove->date,
                                    'display_type' => DisplayType::PRODUCT,
                                    'parent_state' => MoveState::DRAFT,
                                    'quantity'     => 1,
                                    'price_unit'   => $amount,
                                    'currency_id'  => $accountMove->currency_id,
                                    'product_id'   => $variantProduct->id,
                                    'uom_id'       => $variantProduct->uom_id,
                                    'creator_id'   => Auth::id(),
                                ]);

                                $serviceFeatures = ProgramFeature::query()
                                    ->where('program_id', $record->program_id)
                                    ->whereIn('service_type', [ServiceType::TechnicalSupport->value, ServiceType::Mail->value])
                                    ->with('product')
                                    ->get()
                                    ->groupBy('service_type');

                                $subscriptionStart = now()->toDateString();
                                $subscriptionEnd = now()->addYear()->toDateString();

                                foreach ([ServiceType::TechnicalSupport->value, ServiceType::Mail->value] as $requiredType) {
                                    $featuresOfType = $serviceFeatures->get($requiredType, collect());

                                    if ($featuresOfType->count() !== 1) {
                                        throw new \RuntimeException('Exactly one feature row is required for '.$requiredType.'.');
                                    }

                                    $feature = $featuresOfType->first();
                                    $serviceProduct = $feature->product;

                                    if (! $serviceProduct) {
                                        throw new \RuntimeException('Feature '.$feature->name.' must be linked to a service product.');
                                    }

                                    if (($serviceProduct->type?->value ?? $serviceProduct->type) !== ProductType::SERVICE->value) {
                                        throw new \RuntimeException('Feature '.$feature->name.' must be linked to a product of type service.');
                                    }

                                    $servicePrice = (float) ($serviceProduct->price ?? 0);

                                    if ($servicePrice <= 0) {
                                        throw new \RuntimeException('Feature '.$feature->name.' has invalid service price.');
                                    }

                                    $accountMove->invoiceLines()->create([
                                        'name'         => $feature->name,
                                        'date'         => $accountMove->date,
                                        'display_type' => DisplayType::PRODUCT,
                                        'parent_state' => MoveState::DRAFT,
                                        'quantity'     => 1,
                                        'price_unit'   => $servicePrice,
                                        'currency_id'  => $accountMove->currency_id,
                                        'product_id'   => $serviceProduct->id,
                                        'uom_id'       => $serviceProduct->uom_id,
                                        'creator_id'   => Auth::id(),
                                    ]);

                                    LicenseSubscription::query()->updateOrCreate(
                                        [
                                            'license_id' => $record->id,
                                            'feature_id' => $feature->id,
                                        ],
                                        [
                                            'service_type' => $feature->service_type,
                                            'start_date'   => $subscriptionStart,
                                            'end_date'     => $subscriptionEnd,
                                            'is_active'    => true,
                                        ]
                                    );
                                }

                                $accountMove = AccountFacade::computeAccountMove($accountMove);
                                $accountMove->refresh();
                                $salesInvoiceNumber = filled($accountMove->name)
                                    ? (string) $accountMove->name
                                    : 'MOVE-'.$accountMove->id;

                                // Create local invoice record linked to the accounting move
                                LicenseInvoice::query()->create([
                                    'license_id'      => $record->id,
                                    'program_id'      => $record->program_id,
                                    'edition_id'      => $edition->id,
                                    'license_plan'    => $plan->value,
                                    'invoice_number'  => $salesInvoiceNumber,
                                    'item_name'       => $itemName,
                                    'quantity'        => 1,
                                    'unit_price'      => $amount,
                                    'amount'          => $amount,
                                    'billed_by'       => Auth::id(),
                                    'billed_at'       => now(),
                                    'notes'           => 'Linked to accounts invoice #'.$salesInvoiceNumber,
                                    'account_move_id' => $accountMove->id,
                                ]);

                                return ['invoiceNumber' => $salesInvoiceNumber, 'accountMove' => $accountMove];
                            });

                            Notification::make()
                                ->title('Invoice created successfully')
                                ->body('Invoice No: '.$result['invoiceNumber'])
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
