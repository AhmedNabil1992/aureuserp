<?php

namespace Webkul\Wifi\Filament\Admin\Resources\VoucherResource\Pages;

use Filament\Resources\Pages\ListRecords;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Webkul\Wifi\Filament\Admin\Resources\VoucherResource;

class ListVouchers extends ListRecords
{
    protected static string $resource = VoucherResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function table(Table $table): Table
    {
        return parent::table($table)
            ->modifyQueryUsing(function (Builder $query): Builder {
                $cloudId = data_get($this->tableFilters, 'cloud_id.value');

                if (blank($cloudId)) {
                    return $query->whereRaw('1 = 0');
                }

                return $query;
            });
    }
}
