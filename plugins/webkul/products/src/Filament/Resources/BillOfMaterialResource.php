<?php

namespace Webkul\Product\Filament\Resources;

use BackedEnum;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Support\Facades\Auth;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\Location;
use Webkul\Product\Enums\BomType;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\BillOfMaterialResource\Pages\CreateBillOfMaterial;
use Webkul\Product\Filament\Resources\BillOfMaterialResource\Pages\EditBillOfMaterial;
use Webkul\Product\Filament\Resources\BillOfMaterialResource\Pages\ListBillOfMaterials;
use Webkul\Product\Models\BillOfMaterial;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;

class BillOfMaterialResource extends Resource
{
    protected static ?string $model = BillOfMaterial::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-rectangle-stack';

    protected static ?int $navigationSort = 5;

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/bill-of-material.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.product');
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Section::make(__('products::filament/resources/bill-of-material.form.sections.general.title'))
                ->schema([
                    Select::make('product_id')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.product'))
                        ->options(fn (): array => Product::query()
                            ->where('type', ProductType::PRODUCT->value)
                            ->whereNull('parent_id')
                            ->orderBy('name')
                            ->pluck('name', 'id')
                            ->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    Select::make('type')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.type'))
                        ->options(BomType::class)
                        ->default(BomType::Manufacture->value)
                        ->required(),
                    TextInput::make('quantity')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.quantity'))
                        ->numeric()
                        ->default(1)
                        ->minValue(0.0001)
                        ->required(),
                    Select::make('uom_id')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.uom'))
                        ->options(fn (): array => UOM::query()->orderBy('name')->pluck('name', 'id')->all())
                        ->searchable()
                        ->preload()
                        ->required(),
                    TextInput::make('reference')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.reference'))
                        ->maxLength(255),
                    Select::make('company_id')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.company'))
                        ->relationship('company', 'name')
                        ->searchable()
                        ->preload()
                        ->default(Auth::user()?->default_company_id),
                    Select::make('source_location_id')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.source_location'))
                        ->options(function (Get $get): array {
                            $companyId = $get('company_id') ?? Auth::user()?->default_company_id;

                            return Location::query()
                                ->where('type', LocationType::INTERNAL)
                                ->where('is_scrap', false)
                                ->where(function ($query) use ($companyId): void {
                                    $query->where('company_id', $companyId)
                                        ->orWhereNull('company_id');
                                })
                                ->orderBy('full_name')
                                ->pluck('full_name', 'id')
                                ->all();
                        })
                        ->searchable()
                        ->preload(),
                    Textarea::make('notes')
                        ->label(__('products::filament/resources/bill-of-material.form.sections.general.fields.notes'))
                        ->rows(3)
                        ->columnSpanFull(),
                ])
                ->columns(2),
            Section::make(__('products::filament/resources/bill-of-material.form.sections.components.title'))
                ->schema([
                    Repeater::make('lines')
                        ->relationship()
                        ->label(__('products::filament/resources/bill-of-material.form.sections.components.title'))
                        ->defaultItems(1)
                        ->schema([
                            Select::make('component_id')
                                ->label(__('products::filament/resources/bill-of-material.form.sections.components.fields.component'))
                                ->options(fn (Get $get): array => Product::query()
                                    ->whereIn('type', [ProductType::GOODS->value, ProductType::PRODUCT->value])
                                    ->whereNull('parent_id')
                                    ->when($get('../../product_id'), fn ($query, $productId) => $query->whereKeyNot($productId))
                                    ->orderBy('name')
                                    ->pluck('name', 'id')
                                    ->all())
                                ->searchable()
                                ->preload()
                                ->required(),
                            TextInput::make('quantity')
                                ->label(__('products::filament/resources/bill-of-material.form.sections.components.fields.quantity'))
                                ->numeric()
                                ->default(1)
                                ->minValue(0.0001)
                                ->required(),
                            Select::make('uom_id')
                                ->label(__('products::filament/resources/bill-of-material.form.sections.components.fields.uom'))
                                ->options(fn (): array => UOM::query()->orderBy('name')->pluck('name', 'id')->all())
                                ->searchable()
                                ->preload(),
                            Textarea::make('notes')
                                ->label(__('products::filament/resources/bill-of-material.form.sections.components.fields.notes'))
                                ->rows(2)
                                ->columnSpanFull(),
                        ])
                        ->columns(3)
                        ->columnSpanFull(),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('product.name')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.product'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('type')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.type')),
                TextColumn::make('quantity')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.quantity'))
                    ->numeric(4),
                TextColumn::make('uom.name')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.uom')),
                TextColumn::make('lines_count')
                    ->counts('lines')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.components')),
                TextColumn::make('company.name')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.company')),
                TextColumn::make('sourceLocation.full_name')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.source_location')),
                TextColumn::make('updated_at')
                    ->label(__('products::filament/resources/bill-of-material.table.columns.updated_at'))
                    ->dateTime()
                    ->sortable(),
            ])
            ->defaultSort('updated_at', 'desc')
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                DeleteBulkAction::make(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListBillOfMaterials::route('/'),
            'create' => CreateBillOfMaterial::route('/create'),
            'edit'   => EditBillOfMaterial::route('/{record}/edit'),
        ];
    }
}
