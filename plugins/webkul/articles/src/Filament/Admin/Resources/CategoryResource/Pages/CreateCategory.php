<?php

namespace Webkul\Article\Filament\Admin\Resources\CategoryResource\Pages;

use Filament\Resources\Pages\CreateRecord;
use Webkul\Article\Filament\Admin\Resources\CategoryResource;

class CreateCategory extends CreateRecord
{
    protected static string $resource = CategoryResource::class;
}
