<?php

namespace Webkul\Article\Filament\Admin\Resources\ArticleResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Article\Filament\Admin\Resources\ArticleResource;

class CreateArticle extends CreateRecord
{
    protected static string $resource = ArticleResource::class;
}
