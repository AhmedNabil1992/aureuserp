<?php

namespace Webkul\Software\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Webkul\Software\Enums\TicketStatus;
use Webkul\Software\Models\Ticket;

class OpenTicketsSidebar extends Component
{
    public int $currentTicketId;

    public function mount(int $currentTicketId): void
    {
        $this->currentTicketId = $currentTicketId;
    }

    public function render(): View
    {
        $tickets = Ticket::query()
            ->with(['partner'])
            ->whereIn('status', [TicketStatus::Open->value, TicketStatus::Pending->value])
            ->orderByDesc('is_unread_admin')
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return view('software::livewire.open-tickets-sidebar', [
            'tickets' => $tickets,
        ]);
    }
}
