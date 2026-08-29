<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\TechnicalSupport\Filament\Admin\Resources\CannedReplyResource;

class ListCannedReplies extends ListRecords
{
    use HasTableViews;

    protected static string $resource = CannedReplyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'active' => PresetView::make(__('technical-support::filament/admin/resources/canned-reply.tabs.active'))
                ->icon('heroicon-m-check-circle')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', true)),

            'inactive' => PresetView::make(__('technical-support::filament/admin/resources/canned-reply.tabs.inactive'))
                ->icon('heroicon-m-x-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_active', false)),

            'archived' => PresetView::make(__('technical-support::filament/admin/resources/canned-reply.tabs.archived'))
                ->icon('heroicon-s-archive-box')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed()),
        ];
    }
}
