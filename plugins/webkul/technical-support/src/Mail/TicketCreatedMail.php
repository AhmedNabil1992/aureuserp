<?php

namespace Webkul\TechnicalSupport\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Webkul\TechnicalSupport\Models\Ticket;

class TicketCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Ticket $ticket) {}

    public function envelope(): Envelope
    {
        $serviceName = $this->ticket->service_label;

        return new Envelope(
            subject: "تذكرة دعم فني جديدة #{$this->ticket->ticket_number} - {$serviceName}",
        );
    }

    public function content(): Content
    {
        $url = route('filament.admin.resources.tickets.view', ['record' => $this->ticket->id]);

        return new Content(
            view: 'technical-support::emails.ticket-created',
            with: [
                'ticket'     => $this->ticket,
                'viewUrl'    => $url,
                'clientName' => $this->ticket->partner?->name ?? 'عميل',
            ]
        );
    }
}
