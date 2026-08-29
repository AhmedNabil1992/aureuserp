<?php

namespace Webkul\TechnicalSupport\Filament\Admin\Resources\TicketEventResource\Pages;

use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Database\Eloquent\Builder;
use Webkul\TableViews\Filament\Components\PresetView;
use Webkul\TableViews\Filament\Concerns\HasTableViews;
use Webkul\TechnicalSupport\Filament\Admin\Resources\TicketEventResource;

class ManageTicketEvents extends ManageRecords
{
    use HasTableViews;

    protected static string $resource = TicketEventResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }

    public function getPresetTableViews(): array
    {
        return [
            'all' => PresetView::make(__('technical-support::filament/admin/resources/ticket-event.tabs.all'))
                ->icon('heroicon-m-bars-3')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query),

            'public' => PresetView::make(__('technical-support::filament/admin/resources/ticket-event.tabs.public'))
                ->icon('heroicon-m-chat-bubble-left-right')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_private', false)),

            'private' => PresetView::make(__('technical-support::filament/admin/resources/ticket-event.tabs.private'))
                ->icon('heroicon-m-eye-slash')
                ->favorite()
                ->modifyQueryUsing(fn (Builder $query) => $query->where('is_private', true)),

            'staff' => PresetView::make(__('technical-support::filament/admin/resources/ticket-event.tabs.staff'))
                ->icon('heroicon-m-user-circle')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('user_id')),

            'customer' => PresetView::make(__('technical-support::filament/admin/resources/ticket-event.tabs.customer'))
                ->icon('heroicon-m-user')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereNotNull('partner_id')),
        ];
    }
}
