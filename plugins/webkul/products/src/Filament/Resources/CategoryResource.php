<?php

namespace Webkul\Product\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\CreateCategory;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\EditCategory;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ListCategories;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ManageProducts;
use Webkul\Product\Filament\Resources\CategoryResource\Pages\ViewCategory;
use Webkul\Product\Filament\Resources\CategoryResource\Schemas\CategoryForm;
use Webkul\Product\Filament\Resources\CategoryResource\Schemas\CategoryInfolist;
use Webkul\Product\Filament\Resources\CategoryResource\Tables\CategoriesTable;
use Webkul\Product\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-folder';

    protected static ?int $navigationSort = 1;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = false;

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/category.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.product');
    }

    public static function form(Schema $schema): Schema
    {
        return CategoryForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return CategoriesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return CategoryInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'    => ListCategories::route('/'),
            'create'   => CreateCategory::route('/create'),
            'view'     => ViewCategory::route('/{record}'),
            'edit'     => EditCategory::route('/{record}/edit'),
            'products' => ManageProducts::route('/{record}/products'),
        ];
    }
}
