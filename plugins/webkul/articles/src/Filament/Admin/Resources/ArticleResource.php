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
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Pages\Enums\SubNavigationPosition;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;
use Webkul\Article\Filament\Admin\Resources\ArticleResource\Pages\CreateArticle;
use Webkul\Article\Filament\Admin\Resources\ArticleResource\Pages\EditArticle;
use Webkul\Article\Filament\Admin\Resources\ArticleResource\Pages\ListArticles;
use Webkul\Article\Filament\Admin\Resources\ArticleResource\Pages\ViewArticle;
use Webkul\Article\Models\Article;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $slug = 'articles/articles';

    protected static ?SubNavigationPosition $subNavigationPosition = SubNavigationPosition::Top;

    protected static ?string $recordTitleAttribute = 'title';

    public static function getNavigationLabel(): string
    {
        return __('articles::filament/admin/resources/article.navigation.title');
    }

    public static function getNavigationGroup(): string
    {
        return __('articles::filament/admin/resources/article.navigation.group');
    }

    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-book-open';
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['title', 'summary', 'content'];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('articles::filament/admin/resources/article.form.sections.general.title'))
                            ->schema([
                                TextInput::make('title')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.title'))
                                    ->required()
                                    ->live(onBlur: true)
                                    ->maxLength(255)
                                    ->afterStateUpdated(fn (string $operation, $state, Set $set) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),
                                TextInput::make('slug')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.slug'))
                                    ->disabled()
                                    ->dehydrated()
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(Article::class, 'slug', ignoreRecord: true),
                                Textarea::make('summary')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.summary'))
                                    ->rows(3),
                                RichEditor::make('content')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.content'))
                                    ->required()
                                    ->fileAttachmentsDisk('public')
                                    ->fileAttachmentsDirectory('articles/attachments'),
                                TextInput::make('video_embed_url')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.video-embed-url'))
                                    ->url()
                                    ->placeholder('https://www.youtube.com/embed/...')
                                    ->maxLength(500),
                                FileUpload::make('cover_image')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.cover-image'))
                                    ->image()
                                    ->disk('public')
                                    ->directory('articles/covers'),
                                FileUpload::make('files')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.general.fields.files'))
                                    ->multiple()
                                    ->disk('public')
                                    ->directory('articles/files')
                                    ->downloadable(),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('articles::filament/admin/resources/article.form.sections.settings.title'))
                            ->schema([
                                Select::make('category_id')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.settings.fields.category'))
                                    ->relationship(
                                        name: 'category',
                                        titleAttribute: 'name',
                                        modifyQueryUsing: fn ($query) => $query->withTrashed(),
                                    )
                                    ->searchable()
                                    ->preload()
                                    ->getOptionLabelFromRecordUsing(fn ($record) => $record->name.($record->trashed() ? ' (Deleted)' : ''))
                                    ->disableOptionWhen(fn ($label) => str_contains($label, ' (Deleted)'))
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255)
                                            ->live(onBlur: true)
                                            ->afterStateUpdated(fn ($state, Set $set) => $set('slug', Str::slug($state))),
                                        TextInput::make('slug')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Select::make('tags')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.settings.fields.tags'))
                                    ->relationship('tags', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->createOptionForm([
                                        TextInput::make('name')
                                            ->required()
                                            ->maxLength(255),
                                    ]),
                                Select::make('programs')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.settings.fields.programs'))
                                    ->relationship('programs', 'name')
                                    ->searchable()
                                    ->preload()
                                    ->multiple()
                                    ->helperText(__('articles::filament/admin/resources/article.form.sections.settings.fields.programs-helper')),
                                Toggle::make('is_internal')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.settings.fields.is-internal'))
                                    ->helperText(__('articles::filament/admin/resources/article.form.sections.settings.fields.is-internal-helper')),
                                Toggle::make('is_published')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.settings.fields.is-published')),
                                DateTimePicker::make('published_at')
                                    ->label(__('articles::filament/admin/resources/article.form.sections.settings.fields.published-at'))
                                    ->visible(fn ($get) => $get('is_published')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('title')
                    ->label(__('articles::filament/admin/resources/article.table.columns.title'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('category.name')
                    ->label(__('articles::filament/admin/resources/article.table.columns.category'))
                    ->searchable()
                    ->sortable(),
                IconColumn::make('is_internal')
                    ->label(__('articles::filament/admin/resources/article.table.columns.is-internal'))
                    ->boolean(),
                IconColumn::make('is_published')
                    ->label(__('articles::filament/admin/resources/article.table.columns.is-published'))
                    ->boolean(),
                TextColumn::make('published_at')
                    ->label(__('articles::filament/admin/resources/article.table.columns.published-at'))
                    ->dateTime()
                    ->sortable(),
                TextColumn::make('creator.name')
                    ->label(__('articles::filament/admin/resources/article.table.columns.creator'))
                    ->sortable(),
                TextColumn::make('created_at')
                    ->label(__('articles::filament/admin/resources/article.table.columns.created-at'))
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('articles::filament/admin/resources/article.table.filters.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
                Filter::make('is_internal')
                    ->label(__('articles::filament/admin/resources/article.table.filters.internal-only'))
                    ->query(fn (Builder $query) => $query->where('is_internal', true)),
                Filter::make('is_customer')
                    ->label(__('articles::filament/admin/resources/article.table.filters.customer-only'))
                    ->query(fn (Builder $query) => $query->where('is_internal', false)),
                Filter::make('is_published')
                    ->label(__('articles::filament/admin/resources/article.table.filters.published'))
                    ->query(fn (Builder $query) => $query->where('is_published', true)),
                TrashedFilter::make(),
            ])
            ->recordActions([
                ActionGroup::make([
                    ViewAction::make(),
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

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make(__('articles::filament/admin/resources/article.infolist.sections.general.title'))
                            ->schema([
                                TextEntry::make('title')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.general.entries.title'))
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('summary')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.general.entries.summary')),
                                TextEntry::make('content')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.general.entries.content'))
                                    ->html(),
                                TextEntry::make('video_embed_url')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.general.entries.video-embed-url'))
                                    ->url(fn ($record) => $record->video_embed_url)
                                    ->openUrlInNewTab()
                                    ->visible(fn ($record) => filled($record->video_embed_url)),
                                ImageEntry::make('cover_image')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.general.entries.cover-image'))
                                    ->disk('public')
                                    ->visible(fn ($record) => filled($record->cover_image)),
                            ]),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('articles::filament/admin/resources/article.infolist.sections.settings.title'))
                            ->schema([
                                TextEntry::make('category.name')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.category')),
                                TextEntry::make('tags.name')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.tags'))
                                    ->badge(),
                                TextEntry::make('programs.name')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.programs'))
                                    ->badge(),
                                IconEntry::make('is_internal')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.is-internal'))
                                    ->boolean(),
                                IconEntry::make('is_published')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.is-published'))
                                    ->boolean(),
                                TextEntry::make('published_at')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.published-at'))
                                    ->dateTime(),
                                TextEntry::make('creator.name')
                                    ->label(__('articles::filament/admin/resources/article.infolist.sections.settings.entries.creator')),
                            ]),
                    ])
                    ->columnSpan(['lg' => 1]),
            ])
            ->columns(3);
    }

    public static function getPages(): array
    {
        return [
            'index'  => ListArticles::route('/'),
            'create' => CreateArticle::route('/create'),
            'view'   => ViewArticle::route('/{record}'),
            'edit'   => EditArticle::route('/{record}/edit'),
        ];
    }
}
