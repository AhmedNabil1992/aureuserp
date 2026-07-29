<?php

namespace Webkul\Psmonitor\Filament\Customer\Actions;

use Filament\Actions\Action;
use Maatwebsite\Excel\Facades\Excel;
use Webkul\Psmonitor\Services\TableQueryExport;

class ExportToExcelAction extends Action
{
    public static function getDefaultName(): ?string
    {
        return 'export_excel';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this
            ->label(__('psmonitor::filament/customer/pages/common.export_excel'))
            ->icon('heroicon-o-arrow-down-tray')
            ->color('success')
            ->action(function ($livewire, $table) {
                $query = method_exists($livewire, 'getFilteredTableQuery')
                    ? $livewire->getFilteredTableQuery()
                    : $table->getQuery();

                $columns = array_filter(
                    $table->getColumns(),
                    fn ($col) => ! $col->isHidden()
                );

                $headings = [];
                $columnList = [];

                foreach ($columns as $column) {
                    $headings[] = (string) $column->getLabel();
                    $columnList[] = $column;
                }

                $fileName = 'export_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

                return Excel::download(
                    new TableQueryExport(
                        $query,
                        $headings,
                        function ($row) use ($columnList) {
                            $mapped = [];

                            foreach ($columnList as $column) {
                                $name = $column->getName();
                                $val = data_get($row, $name);

                                if (is_array($val) || is_object($val)) {
                                    $val = json_encode($val, JSON_UNESCAPED_UNICODE);
                                }

                                $mapped[] = $val ?? '-';
                            }

                            return $mapped;
                        }
                    ),
                    $fileName
                );
            });
    }
}
