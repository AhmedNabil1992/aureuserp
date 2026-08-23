<?php

namespace Webkul\TechnicalSupport\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Models\TicketEvent;

class TicketMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public ?TicketEvent $event = null
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('tickets.' . $this->ticket->id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'TicketMessageSent';
    }

    public function broadcastWith(): array
    {
        return [
            'ticket_id' => $this->ticket->id,
            'event_id'  => $this->event?->id,
        ];
    }
}
