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
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\ColorColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Webkul\Article\Filament\Admin\Resources\TagResource\Pages\CreateTag;
use Webkul\Article\Filament\Admin\Resources\TagResource\Pages\EditTag;
use Webkul\Article\Filament\Admin\Resources\TagResource\Pages\ListTags;
use Webkul\Article\Models\Tag;

class TagResource extends Resource
{
    protected static ?string $model = Tag::class;

    protected static ?string $slug = 'articles/tags';

    protected static ?string $recordTitleAttribute = 'name';

    public static function getNavigationLabel(): string
    {
        return __('articles::filament/admin/resources/tag.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('articles::filament/admin/resources/tag.navigation.group');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-tag';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make(__('articles::filament/admin/resources/tag.form.sections.general.title'))
                    ->schema([
                        TextInput::make('name')
                            ->label(__('articles::filament/admin/resources/tag.form.sections.general.fields.name'))
                            ->required()
                            ->maxLength(255),
                        ColorPicker::make('color')
                            ->label(__('articles::filament/admin/resources/tag.form.sections.general.fields.color')),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ColorColumn::make('color')
                    ->label(__('articles::filament/admin/resources/tag.table.columns.color')),
                TextColumn::make('name')
                    ->label(__('articles::filament/admin/resources/tag.table.columns.name'))
                    ->searchable()
                    ->sortable(),
                TextColumn::make('articles_count')
                    ->label(__('articles::filament/admin/resources/tag.table.columns.articles-count'))
                    ->counts('articles')
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('articles::filament/admin/resources/tag.table.columns.created-at'))
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
            'index'  => ListTags::route('/'),
            'create' => CreateTag::route('/create'),
            'edit'   => EditTag::route('/{record}/edit'),
        ];
    }
}
