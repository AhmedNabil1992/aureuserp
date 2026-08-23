<?php

namespace Webkul\TechnicalSupport\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webkul\TechnicalSupport\Events\TicketMessageSent;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Models\TicketAttachment;
use Webkul\TechnicalSupport\Models\TicketEvent;

class TicketService
{
    public function __construct(
        protected TicketNotificationService $notificationService
    ) {}

    /**
     * Generate the next available ticket number.
     */
    public function generateTicketNumber(): int
    {
        return (Ticket::withTrashed()->max('ticket_number') ?? 0) + 1;
    }

    /**
     * Create a new ticket and save its attachments.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $filePaths
     */
    public function createTicket(array $data, array $filePaths = []): Ticket
    {
        $data['ticket_number'] = $this->generateTicketNumber();

        if (isset($data['content'])) {
            $data['content'] = $this->sanitizeHtml($data['content']);
        }

        $ticket = Ticket::create($data);

        $this->saveAttachments($ticket, $filePaths);

        // Broadcast real-time Reverb event
        TicketMessageSent::dispatch($ticket);

        // Send notifications to assigned staff
        $this->notificationService->notifyStaffNewTicket($ticket);

        return $ticket;
    }

    /**
     * Add a reply event to an existing ticket.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $filePaths
     */
    public function replyToTicket(Ticket $ticket, array $data, array $filePaths = []): TicketEvent
    {
        if (isset($data['content'])) {
            $data['content'] = $this->sanitizeHtml($data['content']);
        }

        $event = $ticket->events()->create($data);

        $this->saveAttachments($event, $filePaths);

        $isAdminReply = ! empty($data['user_id']);

        if ($isAdminReply) {
            $ticket->update(['is_unread_client' => true]);
            $this->notificationService->notifyCustomerNewReply($ticket, $event);
        } else {
            $ticket->update(['is_unread_admin' => true]);
            $this->notificationService->notifyStaffNewReply($ticket, $event);
        }

        // Broadcast real-time event
        TicketMessageSent::dispatch($ticket, $event);

        return $event;
    }

    /**
     * Persist file paths as TicketAttachment records.
     *
     * @param  array<int, string>  $filePaths
     */
    public function saveAttachments(Ticket|TicketEvent $attachable, array $filePaths): void
    {
        foreach ($filePaths as $path) {
            if (! $path || ! Storage::disk('public')->exists($path)) {
                continue;
            }

            $mime = Storage::disk('public')->mimeType($path) ?: null;
            $size = Storage::disk('public')->size($path);
            $name = basename($path);

            $attachable->attachments()->create([
                'file_path'     => $path,
                'original_name' => $name,
                'mime_type'     => $mime,
                'file_size'     => $size,
            ]);
        }
    }

    /**
     * Store an uploaded file.
     */
    public function storeUploadedFile(UploadedFile $file): string
    {
        return $file->store('technical-support/tickets', 'public');
    }

    /**
     * Sanitize HTML content.
     */
    private function sanitizeHtml(string $content): string
    {
        $allowedTags = '<p><br><b><i><strong><em><u><s><ul><ol><li><a>'
            .'<h1><h2><h3><h4><h5><h6><blockquote><pre><code><span><div>'
            .'<table><thead><tbody><tr><th><td><img><audio>';

        $content = strip_tags($content, $allowedTags);

        // Remove on* event-handler attributes
        $content = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $content) ?? $content;

        // Remove javascript: protocol in href/src/action attributes
        $content = preg_replace('/\s+(href|src|action)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $content) ?? $content;

        return $content;
    }
}
