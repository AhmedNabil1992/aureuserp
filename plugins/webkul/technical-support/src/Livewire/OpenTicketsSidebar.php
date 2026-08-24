<?php

namespace Webkul\TechnicalSupport\Livewire;

use Illuminate\Contracts\View\View;
use Livewire\Component;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Models\Ticket;

class OpenTicketsSidebar extends Component
{
    public int $currentTicketId;

    public function mount(int $currentTicketId): void
    {
        $this->currentTicketId = $currentTicketId;
    }

    protected function getListeners(): array
    {
        return [
            // غيرناها لـ echo-private عشان الحماية
            'echo-private:tickets.admin-sidebar,.TicketMessageSent' => '$refresh',
            
            // لو عندك Event لإنشاء التيكت ضيفه هنا كمان بالشكل ده:
            // 'echo-private:tickets.admin-sidebar,.TicketCreated' => '$refresh',
        ];
    }

    public function render(): View
    {
        $tickets = Ticket::query()
            ->with(['partner', 'events'])
            ->whereIn('status', [TicketStatus::Open->value, TicketStatus::Pending->value])
            ->orderByDesc('is_unread_admin')
            ->orderByDesc('updated_at')
            ->limit(50)
            ->get();

        return view('technical-support::livewire.open-tickets-sidebar', [
            'tickets' => $tickets,
        ]);
    }
}