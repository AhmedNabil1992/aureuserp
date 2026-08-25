<?php

namespace Webkul\TechnicalSupport\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Models\TicketEvent;

class TicketReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Ticket $ticket,
        public TicketEvent $event,
        public string $recipientType = 'customer' // 'customer' or 'staff'
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: "رد جديد على التذكرة #{$this->ticket->ticket_number} - {$this->ticket->title}",
        );
    }

    public function content(): Content
    {
        $url = $this->recipientType === 'customer'
            ? route('filament.customer.resources.support-tickets.view', ['record' => $this->ticket->id])
            : route('filament.admin.resources.tickets.view', ['record' => $this->ticket->id]);

        $senderName = $this->recipientType === 'customer'
            ? ($this->event->user?->name ?? 'فريق الدعم الفني')
            : ($this->event->partner?->name ?? 'العميل');

        return new Content(
            view: 'technical-support::emails.ticket-reply',
            with: [
                'ticket'        => $this->ticket,
                'event'         => $this->event,
                'viewUrl'       => $url,
                'senderName'    => $senderName,
                'recipientType' => $this->recipientType,
            ]
        );
    }
}
