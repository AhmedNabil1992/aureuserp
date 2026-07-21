<?php

namespace Webkul\Psmonitor\Filament\Customer\Concerns;

use Filament\Tables;

/**
 * Combined trait for Page classes that manually use InteractsWithTable.
 * Resolves the PHP trait method conflict on getTableRecordsPerPageSelectOptions()
 * between HasRemoteTablePagination and CanPaginateRecords (via InteractsWithTable).
 */
trait HasRemoteTablePaginationForPage
{
    use HasRemoteTablePagination, Tables\Concerns\InteractsWithTable {
        HasRemoteTablePagination::getTableRecordsPerPageSelectOptions insteadof Tables\Concerns\InteractsWithTable;
    }
}
