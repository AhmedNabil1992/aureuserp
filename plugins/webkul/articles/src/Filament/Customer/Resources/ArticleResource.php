<?php

namespace Webkul\Article\Filament\Customer\Resources;

use Filament\Actions\ViewAction;
use Filament\Infolists\Components\ImageEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Group;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;
use Filament\Support\Enums\TextSize;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Webkul\Article\Filament\Customer\Resources\ArticleResource\Pages\ListArticles;
use Webkul\Article\Filament\Customer\Resources\ArticleResource\Pages\ViewArticle;
use Webkul\Article\Models\Article;
use Webkul\Software\Models\License;

class ArticleResource extends Resource
{
    protected static ?string $model = Article::class;

    protected static ?string $slug = 'articles';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-book-open';

    protected static ?int $navigationSort = 20;

    public static function getNavigationLabel(): string
    {
        return __('articles::filament/customer/resources/article.navigation.title');
    }

    public static function getModelLabel(): string
    {
        return __('articles::filament/customer/resources/article.model-label');
    }

    public static function getPluralModelLabel(): string
    {
        return __('articles::filament/customer/resources/article.plural-model-label');
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                Group::make()
                    ->schema([
                        Section::make()
                            ->schema([
                                TextEntry::make('title')
                                    ->hiddenLabel()
                                    ->size(TextSize::Large)
                                    ->weight(FontWeight::Bold),
                                TextEntry::make('summary')
                                    ->hiddenLabel()
                                    ->visible(fn ($record) => filled($record->summary)),
                                ImageEntry::make('cover_image')
                                    ->hiddenLabel()
                                    ->disk('public')
                                    ->visible(fn ($record) => filled($record->cover_image)),
                                TextEntry::make('content')
                                    ->hiddenLabel()
                                    ->html(),
                            ]),

                        Section::make(__('articles::filament/customer/resources/article.infolist.sections.video.title'))
                            ->schema([
                                TextEntry::make('video_embed_url')
                                    ->hiddenLabel()
                                    ->url(fn ($record) => $record->video_embed_url)
                                    ->openUrlInNewTab(),
                            ])
                            ->visible(fn ($record) => filled($record->video_embed_url)),
                    ])
                    ->columnSpan(['lg' => 2]),

                Group::make()
                    ->schema([
                        Section::make(__('articles::filament/customer/resources/article.infolist.sections.details.title'))
                            ->schema([
                                TextEntry::make('category.name')
                                    ->label(__('articles::filament/customer/resources/article.infolist.sections.details.entries.category'))
                                    ->badge(),
                                TextEntry::make('tags.name')
                                    ->label(__('articles::filament/customer/resources/article.infolist.sections.details.entries.tags'))
                                    ->badge(),
                                TextEntry::make('published_at')
                                    ->label(__('articles::filament/customer/resources/article.infolist.sections.details.entries.published-at'))
                                    ->date(),
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
                    ->label(__('articles::filament/customer/resources/article.table.columns.title'))
                    ->searchable()
                    ->sortable()
                    ->weight(FontWeight::Medium),
                TextColumn::make('category.name')
                    ->label(__('articles::filament/customer/resources/article.table.columns.category'))
                    ->badge()
                    ->sortable(),
                TextColumn::make('tags.name')
                    ->label(__('articles::filament/customer/resources/article.table.columns.tags'))
                    ->badge()
                    ->separator(', '),
                TextColumn::make('published_at')
                    ->label(__('articles::filament/customer/resources/article.table.columns.published-at'))
                    ->date()
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('category_id')
                    ->label(__('articles::filament/customer/resources/article.table.filters.category'))
                    ->relationship('category', 'name')
                    ->searchable()
                    ->preload(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->modifyQueryUsing(function (Builder $query): Builder {
                $partnerId = Auth::guard('customer')->id();

                $programIds = License::query()
                    ->where('partner_id', $partnerId)
                    ->where('is_active', true)
                    ->pluck('program_id');

                return $query
                    ->where('is_internal', false)
                    ->where('is_published', true)
                    ->where(function (Builder $q) use ($programIds): void {
                        $q->doesntHave('programs')
                            ->orWhereHas('programs', fn (Builder $pq) => $pq->whereIn('software_programs.id', $programIds));
                    });
            });
    }

    public static function getPages(): array
    {
        return [
            'index' => ListArticles::route('/'),
            'view'  => ViewArticle::route('/{record}'),
        ];
    }
}
