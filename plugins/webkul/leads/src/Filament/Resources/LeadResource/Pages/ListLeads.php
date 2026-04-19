<?php

namespace Webkul\Lead\Filament\Resources\LeadResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Webkul\Lead\Filament\Resources\LeadResource;
use Webkul\Lead\Filament\Widgets\LeadsStatsWidget;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getHeaderWidgets(): array
    {
        return [
            LeadsStatsWidget::make(),
        ];
    }
}
