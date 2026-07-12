<?php

namespace Webkul\Software\Filament\Admin\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Webkul\Product\Models\Product;
use Webkul\Software\Filament\Admin\Clusters\Catalog;
use Webkul\Software\Filament\Admin\Resources\ProgramEditionResource\Pages\ManageProgramEditions;
use Webkul\Software\Models\Program;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Models\ProgramFeature;

class ProgramEditionResource extends Resource
{
    protected static ?string $model = ProgramEdition::class;

    protected static ?string $slug = 'program-editions';

    protected static ?int $navigationSort = 2;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?string $cluster = Catalog::class;

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.software');
    }

    public static function getNavigationLabel(): string
    {
        return __('software::filament/admin/resources/program-edition.navigation.label');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('program_id')
                ->relationship('program', 'name')
                ->searchable()
                ->preload()
                ->required()
                ->live()
                ->afterStateUpdated(fn (Set $set): mixed => $set('variant_product_id', null)),
            TextInput::make('name')->required()->maxLength(100),
            Select::make('variant_product_id')
                ->label(__('software::filament/admin/resources/program-edition.form.fields.linked_variant'))
                ->options(function (Get $get): array {
                    $programId = $get('program_id');

                    if (! $programId) {
                        return [];
                    }

                    $program = Program::query()->find($programId);

                    if (! $program?->product_id) {
                        return [];
                    }

                    return Product::query()
                        ->where('parent_id', $program->product_id)
                        ->orderBy('name')
                        ->pluck('name', 'id')
                        ->all();
                })
                ->searchable()
                ->preload()
                ->nullable()
                ->helperText(__('software::filament/admin/resources/program-edition.form.helper_text.linked_variant')),
            TextInput::make('max_devices')->numeric()->minValue(1),
            TextInput::make('license_cost')->numeric(),
            TextInput::make('license_price')->numeric(),
            TextInput::make('monthly_renewal')->numeric(),
            TextInput::make('annual_renewal')->numeric(),
            Section::make(__('software::filament/admin/resources/program-edition.form.feature_rules.title'))
                ->schema([
                    Repeater::make('featureRules')
                        ->relationship()
                        ->hiddenLabel()
                        ->reorderable('sort_order')
                        ->addActionLabel(__('software::filament/admin/resources/program-edition.form.feature_rules.add_action'))
                        ->schema([
                            Select::make('program_feature_id')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.feature'))
                                ->options(function (Get $get): array {
                                    $programId = $get('../../program_id');

                                    if (! $programId) {
                                        return [];
                                    }

                                    return ProgramFeature::query()
                                        ->where('program_id', $programId)
                                        ->orderBy('name')
                                        ->pluck('name', 'id')
                                        ->all();
                                })
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('price')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.price'))
                                ->numeric()
                                ->minValue(0)
                                ->helperText(__('software::filament/admin/resources/program-edition.form.feature_rules.helper_text.price')),
                            Toggle::make('auto_attach_on_final_license')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.auto_attach_on_final_license'))
                                ->default(false),
                            Toggle::make('is_complimentary')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.is_complimentary'))
                                ->default(false)
                                ->live()
                                ->afterStateUpdated(function (bool $state, Set $set): void {
                                    if ($state) {
                                        $set('invoice_on_initial_billing', false);
                                    }
                                }),
                            Toggle::make('invoice_on_initial_billing')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.invoice_on_initial_billing'))
                                ->default(true)
                                ->disabled(fn (Get $get): bool => (bool) $get('is_complimentary')),
                            Toggle::make('invoice_on_renewal')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.invoice_on_renewal'))
                                ->default(true),
                            Toggle::make('auto_renew_with_license')
                                ->label(__('software::filament/admin/resources/program-edition.form.feature_rules.fields.auto_renew_with_license'))
                                ->default(true),
                        ])
                        ->columns(2)
                        ->columnSpanFull(),
                ])
                ->columnSpanFull(),
        ])->columns(2);
    }

    public static function table(Table $table): Table
    {
        return $table->columns([
            TextColumn::make('program.name')->label(__('software::filament/admin/resources/program-edition.table.columns.program'))->searchable(),
            TextColumn::make('name')->label(__('software::filament/admin/resources/program-edition.table.columns.name'))->searchable(),
            TextColumn::make('variantProduct.name')->label(__('software::filament/admin/resources/program-edition.table.columns.variant'))->searchable(),
            TextColumn::make('max_devices')->label(__('software::filament/admin/resources/program-edition.table.columns.max_devices'))->numeric(),
            TextColumn::make('license_price')->label(__('software::filament/admin/resources/program-edition.table.columns.license_price'))->money('EGP'),
            TextColumn::make('license_cost')->label(__('software::filament/admin/resources/program-edition.table.columns.license_cost'))->money('EGP'),
            TextColumn::make('monthly_renewal')->label(__('software::filament/admin/resources/program-edition.table.columns.monthly_renewal'))->money('EGP'),
            TextColumn::make('annual_renewal')->label(__('software::filament/admin/resources/program-edition.table.columns.annual_renewal'))->money('EGP'),
            TextColumn::make('created_at')->label(__('software::filament/admin/resources/program-edition.table.columns.created_at'))->dateTime()->sortable(),
            TextColumn::make('updated_at')->label(__('software::filament/admin/resources/program-edition.table.columns.updated_at'))->dateTime()->sortable(),
        ])->recordActions([
            EditAction::make(),
            DeleteAction::make(),
        ])->toolbarActions([
            DeleteBulkAction::make(),
        ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageProgramEditions::route('/'),
        ];
    }
}
