<?php

namespace Webkul\Software\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Webkul\Software\Models\Ticket;
use Webkul\Software\Models\TicketEvent;
use Webkul\Software\Services\FirebaseNotificationService;

class NotifyTicketUpdate implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly Ticket $ticket,
        public readonly TicketEvent $event,
    ) {}

    public function handle(FirebaseNotificationService $fcm): void
    {
        $isAdminReply = ! is_null($this->event->user_id);

        if ($isAdminReply) {
            // Admin replied → notify the customer
            $senderName = $this->event->user?->name ?? 'Support Team';

            $fcm->notifyCustomer(
                $this->ticket,
                'New reply on Ticket #'.$this->ticket->ticket_number,
                $senderName.': '.strip_tags(mb_substr($this->event->content, 0, 100)),
            );
        } else {
            // Customer replied → notify all admins
            $senderName = $this->event->partner?->name ?? 'Customer';

            $fcm->notifyAdmins(
                $this->ticket,
                'Customer replied on Ticket #'.$this->ticket->ticket_number,
                $senderName.': '.strip_tags(mb_substr($this->event->content, 0, 100)),
            );
        }
    }
}
