<?php

namespace Webkul\Software\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Contract\Database as FirebaseDatabase;
use Webkul\Software\Jobs\NotifyTicketUpdate;
use Webkul\Software\Models\Ticket;
use Webkul\Software\Models\TicketAttachment;
use Webkul\Software\Models\TicketEvent;

class TicketService
{
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
     * @param  array<int, string>  $filePaths  Already-stored file paths from FileUpload
     */
    public function createTicket(array $data, array $filePaths = []): Ticket
    {
        $data['ticket_number'] = $this->generateTicketNumber();

        $ticket = Ticket::create($data);

        $this->saveAttachments($ticket, $filePaths);

        return $ticket;
    }

    /**
     * Add a reply event to an existing ticket.
     *
     * @param  array<string, mixed>  $data
     * @param  array<int, string>  $filePaths  Already-stored file paths from FileUpload
     */
    public function replyToTicket(Ticket $ticket, array $data, array $filePaths = []): TicketEvent
    {
        $event = $ticket->events()->create($data);

        $this->saveAttachments($event, $filePaths);

        // Mark ticket as unread for the appropriate side
        if (! empty($data['user_id'])) {
            $ticket->update(['is_unread_client' => true]);
        } else {
            $ticket->update(['is_unread_admin' => true]);
        }

        // Dispatch push notification via Firebase (queued)
        NotifyTicketUpdate::dispatch($ticket, $event->load(['user', 'partner']));

        // Write a tiny signal to Firebase RTDB so open browser/app sessions
        // refresh in real-time without polling.
        $this->signalRtdb($ticket, $event);

        return $event;
    }

    /**
     * Persist file paths as TicketAttachment records for any attachable model.
     *
     * @param  array<int, string>  $filePaths
     */
    public function saveAttachments(Ticket|TicketEvent $attachable, array $filePaths): void
    {
        foreach ($filePaths as $path) {
            if (! $path || ! Storage::exists($path)) {
                continue;
            }

            $mime = Storage::mimeType($path) ?: null;
            $size = Storage::size($path);
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
     * Store an uploaded file to the ticket storage disk and return its path.
     */
    public function storeUploadedFile(UploadedFile $file): string
    {
        return $file->store('software/tickets', 'public');
    }

    /**
     * Write a lightweight timestamp signal to Firebase Realtime Database.
     * Browser / Flutter clients listen to this path and trigger a local
     * refresh when it changes — zero polling, instant updates.
     *
     * Path: tickets/{ticket_id}/last_event
     */
    private function signalRtdb(Ticket $ticket, TicketEvent $event): void
    {
        try {
            /** @var FirebaseDatabase $db */
            $db = app(FirebaseDatabase::class);

            $db->getReference('tickets/'.$ticket->id.'/last_event')
                ->set([
                    'event_id'   => $event->id,
                    'updated_at' => now()->toIso8601String(),
                ]);
        } catch (\Throwable) {
            // Never block a reply because Firebase is unavailable
        }
    }
}
