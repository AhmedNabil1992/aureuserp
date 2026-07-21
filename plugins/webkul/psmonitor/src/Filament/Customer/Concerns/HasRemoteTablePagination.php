<?php

namespace Webkul\Psmonitor\Filament\Customer\Concerns;

use Filament\Tables\Table;

trait HasRemoteTablePagination
{
    protected function getTableRecordsPerPageSelectOptions(): ?array
    {
        return [10, 20, 30, 40, 50];
    }

    protected static function applyRemoteTablePagination(Table $table): Table
    {
        return $table
            ->paginated([10, 20, 30, 40, 50])
            ->defaultPaginationPageOption(10);
    }
}
