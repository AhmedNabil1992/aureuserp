<?php

namespace Webkul\Article\Filament\Customer\Resources\ArticleResource\Pages;

use Filament\Actions\Action;
use Filament\Resources\Pages\ViewRecord;
use Webkul\Article\Filament\Customer\Resources\ArticleResource;

class ViewArticle extends ViewRecord
{
    protected static string $resource = ArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label(__('articles::filament/customer/resources/article.pages.view.actions.back'))
                ->url(fn () => static::getResource()::getUrl('index'))
                ->color('gray'),
        ];
    }
}
