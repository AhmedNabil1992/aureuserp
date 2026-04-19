<?php

namespace Webkul\Article\Filament\Customer\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Webkul\Article\Filament\Customer\Resources\ArticleResource;

class ListArticles extends ListRecords
{
    protected static string $resource = ArticleResource::class;
}
