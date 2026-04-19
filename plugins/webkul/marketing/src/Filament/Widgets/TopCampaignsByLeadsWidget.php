<?php

namespace Webkul\Marketing\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Webkul\Marketing\Filament\Resources\CampaignResource;
use Webkul\Marketing\Models\Campaign;

class TopCampaignsByLeadsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    public function getHeading(): string
    {
        return __('marketing::filament/widgets/top-campaigns-by-leads.heading');
    }

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Campaign::query()
                    ->withCount('leads')
                    ->orderByDesc('leads_count')
                    ->limit(10)
            )
            ->columns([
                TextColumn::make('name')
                    ->label(__('marketing::filament/widgets/top-campaigns-by-leads.columns.campaign'))
                    ->searchable()
                    ->url(fn ($record) => CampaignResource::getUrl('view', ['record' => $record])),
                TextColumn::make('platform')
                    ->label(__('marketing::filament/widgets/top-campaigns-by-leads.columns.platform'))
                    ->badge(),
                TextColumn::make('status')
                    ->label(__('marketing::filament/widgets/top-campaigns-by-leads.columns.status'))
                    ->badge(),
                TextColumn::make('leads_count')
                    ->label(__('marketing::filament/widgets/top-campaigns-by-leads.columns.leads-count'))
                    ->badge()
                    ->color('success')
                    ->sortable(),
                TextColumn::make('month')
                    ->label(__('marketing::filament/widgets/top-campaigns-by-leads.columns.month'))
                    ->date('M Y'),
            ]);
    }
}
