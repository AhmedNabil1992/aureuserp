<?php

namespace Webkul\Article\Filament\Admin\Resources;

use Filament\Actions\ActionGroup;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\ForceDeleteBulkAction;
use Filament\Actions\RestoreAction;
use Filament\Actions\RestoreBulkAction;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Webkul\Article\Filament\Admin\Resources\CategoryResource\Pages\CreateCategory;
use Webkul\Article\Filament\Admin\Resources\CategoryResource\Pages\EditCategory;
use Webkul\Article\Filament\Admin\Resources\CategoryResource\Pages\ListCategories;
use Webkul\Article\Models\Category;

class CategoryResource extends Resource
{
    protected static ?string $model = Category::class;

    protected static ?string $slug = 'articles/categories';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('articles::filament/admin/resources/category.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('admin.navigation.articles');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-folder';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('articles::filament/admin/resources/category.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('articles::filament/admin/resources/category.form.sections.general.fields.name'))
                            ->required()
                            ->live(onBlur: true)
                            ->maxLength(255)
                            ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                        TextInput::make('slug')
                            ->label(__('articles::filament/admin/resources/category.form.sections.general.fields.slug'))
                            ->disabled()
                            ->dehydrated()
                            ->required()
                            ->maxLength(255)
                            ->unique(Category::class, 'slug', ignoreRecord: true),
                        Textarea::make('description')
                            ->label(__('articles::filament/admin/resources/category.form.sections.general.fields.description'))
                            ->rows(3),
                        ColorPicker::make('color')
                            ->label(__('articles::filament/admin/resources/category.form.sections.general.fields.color')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(__('articles::filament/admin/resources/category.table.columns.color')),
                TextColumn::make('name')
                    ->label(__('articles::filament/admin/resources/category.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('articles_count')
                    ->label(__('articles::filament/admin/resources/category.table.columns.articles-count'))
                    ->counts('articles')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('articles::filament/admin/resources/category.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    EditAction::make(),
                    DeleteAction::make(),
                    ForceDeleteAction::make(),
                    RestoreAction::make(),
                ]),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([SoftDeletingScope::class]));
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListCategories::route('/'),
            'create' => CreateCategory::route('/create'),
            'edit'   => EditCategory::route('/{record}/edit'),
        ];
    }
}
