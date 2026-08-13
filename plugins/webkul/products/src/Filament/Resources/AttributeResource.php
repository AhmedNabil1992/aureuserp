<?php

namespace Webkul\Product\Filament\Resources;

use BackedEnum;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Enums\AttributeType;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\CreateAttribute;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\EditAttribute;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ListAttributes;
use Webkul\Product\Filament\Resources\AttributeResource\Pages\ViewAttribute;
use Webkul\Product\Filament\Resources\AttributeResource\Schemas\AttributeForm;
use Webkul\Product\Filament\Resources\AttributeResource\Schemas\AttributeInfolist;
use Webkul\Product\Filament\Resources\AttributeResource\Tables\AttributesTable;
use Webkul\Product\Models\Attribute;

class AttributeResource extends Resource
{
    protected static ?string $model = Attribute::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-swatch';

    protected static ?int $navigationSort = 4;

    protected static bool $shouldRegisterNavigation = true;

    protected static bool $isGloballySearchable = false;

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/attribute.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.product');
    }

    public static function form(Schema $schema): Schema
    {
        return AttributeForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return AttributesTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return AttributeInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListAttributes::route('/'),
            'create' => CreateAttribute::route('/create'),
            'view'   => ViewAttribute::route('/{record}'),
            'edit'   => EditAttribute::route('/{record}/edit'),
        ];
    }
}
