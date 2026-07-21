<?php

namespace Webkul\Product\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Filament\Resources\ProductResource\Schemas\ProductForm;
use Webkul\Product\Filament\Resources\ProductResource\Schemas\ProductInfolist;
use Webkul\Product\Filament\Resources\ProductResource\Support\ProductSchemaRegistry;
use Webkul\Product\Filament\Resources\ProductResource\Tables\ProductsTable;
use Webkul\Product\Models\Product;
use Webkul\Support\Models\UOM;
use Filament\Resources\Pages\Page;

class ProductResource extends Resource
{
    protected static ?string $model = Product::class;

    protected static bool $shouldRegisterNavigation = true;

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'name';

    protected static bool $isGloballySearchable = false;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-shopping-bag';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/product.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.product');
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['name', 'reference', 'barcode'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            __('products::filament/resources/product.global-search.reference') => $record->reference,
            __('products::filament/resources/product.global-search.barcode')   => $record->barcode,
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return ProductForm::configure($schema);
    }

    public static function infolist(Schema $schema): Schema
    {
        return ProductInfolist::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return ProductsTable::configure($table);
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(array_merge(['uom', 'uomPO'], ProductSchemaRegistry::eagerLoads()));
    }

    public static function getDefaultUomIdByProductType(ProductType|string|null $type): ?int
    {
        if (is_string($type)) {
            $type = ProductType::tryFrom($type);
        }

        if ($type === ProductType::SERVICE) {
            $hoursUomId = UOM::query()
                ->whereHas('category', fn (Builder $query) => $query->where('name', 'Working Time'))
                ->whereRaw('LOWER(name) = ?', ['hours'])
                ->orderBy('id')
                ->value('id');

            if ($hoursUomId) {
                return $hoursUomId;
            }
        }

        $categoryName = $type === ProductType::SERVICE ? 'Working Time' : 'Unit';

        return UOM::query()
            ->whereHas('category', fn (Builder $query) => $query->where('name', $categoryName))
            ->orderBy('id')
            ->value('id')
            ?? UOM::query()->orderBy('id')->value('id');
    }

    // public static function getRecordSubNavigation(Page $page): array
    // {
    //     return $page->generateNavigationItems([
    //         ViewProduct::class,
    //         EditProduct::class,
    //         ManageAttributes::class,
    //         ManageVariants::class,
    //     ]);
    // }

    // public static function getPages(): array
    // {
    //     return [
    //         'index'      => ListProducts::route('/'),
    //         'create'     => CreateProduct::route('/create'),
    //         'view'       => ViewProduct::route('/{record}'),
    //         'edit'       => EditProduct::route('/{record}/edit'),
    //         'attributes' => ManageAttributes::route('/{record}/attributes'),
    //         'variants'   => ManageVariants::route('/{record}/variants'),
    //     ];
    // }
}
