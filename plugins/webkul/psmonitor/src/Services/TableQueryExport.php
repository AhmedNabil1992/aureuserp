<?php

namespace Webkul\Psmonitor\Services;

use Closure;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class TableQueryExport implements FromQuery, WithHeadings, WithMapping
{
    public function __construct(
        protected $query,
        protected array $headings,
        protected Closure $mapCallback
    ) {}

    public function query()
    {
        return $this->query;
    }

    public function headings(): array
    {
        return $this->headings;
    }

    public function map($row): array
    {
        return ($this->mapCallback)($row);
    }
}
