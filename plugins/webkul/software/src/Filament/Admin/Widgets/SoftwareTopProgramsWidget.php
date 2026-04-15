<?php

namespace Webkul\Software\Filament\Admin\Widgets;

use BezhanSalleh\FilamentShield\Traits\HasWidgetShield;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\Concerns\InteractsWithPageFilters;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Webkul\Software\Models\Program;

class SoftwareTopProgramsWidget extends BaseWidget
{
    use HasWidgetShield, InteractsWithPageFilters;

    protected static ?int $sort = 8;

    protected int|string|array $columnSpan = 'full';

    protected static bool $isLazy = false;

    protected static ?string $pollingInterval = '15s';

    protected static function getPagePermission(): ?string
    {
        return 'widget_software_software_top_programs_widget';
    }

    public function getHeading(): string|Htmlable|null
    {
        return __('software::filament/admin/widgets/dashboard.top_programs.heading');
    }

    public function table(Table $table): Table
    {
        $query = Program::query()
            ->withCount([
                'licenses as licenses_count'        => fn (Builder $query): Builder => $this->applyCreatedAtFilters($query),
                'licenses as active_licenses_count' => fn (Builder $query): Builder => $this->applyCreatedAtFilters($query)
                    ->where('is_active', true),
                'tickets as tickets_count' => fn (Builder $query): Builder => $this->applyCreatedAtFilters($query),
            ])
            ->orderByDesc('licenses_count')
            ->limit(10);

        return $table
            ->query($query)
            ->defaultPaginationPageOption(5)
            ->columns([
                TextColumn::make('name')
                    ->label(__('software::filament/admin/widgets/dashboard.top_programs.columns.program'))
                    ->searchable(),
                TextColumn::make('licenses_count')
                    ->label(__('software::filament/admin/widgets/dashboard.top_programs.columns.licenses'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('active_licenses_count')
                    ->label(__('software::filament/admin/widgets/dashboard.top_programs.columns.active_licenses'))
                    ->numeric()
                    ->sortable(),
                TextColumn::make('tickets_count')
                    ->label(__('software::filament/admin/widgets/dashboard.top_programs.columns.tickets'))
                    ->numeric()
                    ->sortable(),
            ]);
    }

    private function applyCreatedAtFilters(Builder $query): Builder
    {
        $startDate = $this->pageFilters['startDate'] ?? null;
        $endDate = $this->pageFilters['endDate'] ?? null;

        return $query
            ->when(
                filled($startDate),
                fn (Builder $builder): Builder => $builder->where('created_at', '>=', Carbon::parse($startDate)->startOfDay())
            )
            ->when(
                filled($endDate),
                fn (Builder $builder): Builder => $builder->where('created_at', '<=', Carbon::parse($endDate)->endOfDay())
            );
    }
}
