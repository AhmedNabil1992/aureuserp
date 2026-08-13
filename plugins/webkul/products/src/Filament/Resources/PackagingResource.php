<?php

namespace Webkul\Product\Filament\Resources;

use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Webkul\Product\Filament\Resources\PackagingResource\Pages\ManagePackagings;
use Webkul\Product\Filament\Resources\PackagingResource\Schemas\PackagingForm;
use Webkul\Product\Filament\Resources\PackagingResource\Schemas\PackagingInfolist;
use Webkul\Product\Filament\Resources\PackagingResource\Tables\PackagingsTable;
use Webkul\Product\Models\Packaging;

class PackagingResource extends Resource
{
    protected static ?string $model = Packaging::class;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-gift';

    protected static ?int $navigationSort = 5;

    protected static bool $shouldRegisterNavigation = true;

    public static function getNavigationLabel(): string
    {
        return __('products::filament/resources/packaging.navigation.title');
    }

    public static function getNavigationGroup(): ?string
    {
        return __('admin.navigation.product');
    }

    public static function form(Schema $schema): Schema
    {
        return PackagingForm::configure($schema);
    }

    public static function table(Table $table): Table
    {
        return PackagingsTable::configure($table);
    }

    public static function infolist(Schema $schema): Schema
    {
        return PackagingInfolist::configure($schema);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManagePackagings::route('/'),
        ];
    }
}
