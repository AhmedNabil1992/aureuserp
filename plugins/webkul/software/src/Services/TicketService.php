<?php

namespace Webkul\Software\Services;

use Filament\Notifications\Actions\Action;
use Filament\Notifications\Notification;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Kreait\Firebase\Contract\Database as FirebaseDatabase;
use Webkul\Security\Models\User;
use Webkul\Software\Events\TicketMessageSent;
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

        if (isset($data['content'])) {
            $data['content'] = $this->sanitizeHtml($data['content']);
        }

        $ticket = Ticket::create($data);

        $this->saveAttachments($ticket, $filePaths);

        // Broadcast real-time Reverb event
        TicketMessageSent::dispatch($ticket);

        // Send Filament notification to admins
        try {
            $admins = User::all();
            $adminUrl = \Webkul\Software\Filament\Admin\Resources\TicketResource::getUrl('view', ['record' => $ticket->id]);

            Notification::make()
                ->title("تذكرة جديدة #{$ticket->ticket_number}")
                ->body(($ticket->partner?->name ?? 'عميل') . ': ' . strip_tags(Str::limit($ticket->title, 60)))
                ->actions([
                    Action::make('view')
                        ->label('عرض التذكرة')
                        ->url($adminUrl),
                ])
                ->sendToDatabase($admins)
                ->broadcast($admins);
        } catch (\Throwable) {}

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
        if (isset($data['content'])) {
            $data['content'] = $this->sanitizeHtml($data['content']);
        }

        $event = $ticket->events()->create($data);

        $this->saveAttachments($event, $filePaths);

        $isAdminReply = ! empty($data['user_id']);

        // Mark ticket as unread for the appropriate side
        if ($isAdminReply) {
            $ticket->update(['is_unread_client' => true]);
        } else {
            $ticket->update(['is_unread_admin' => true]);
        }

        // Dispatch push notification via Firebase (queued)
        NotifyTicketUpdate::dispatch($ticket, $event->load(['user', 'partner']));

        // Write a tiny signal to Firebase RTDB
        $this->signalRtdb($ticket, $event);

        // Broadcast real-time Reverb event immediately
        TicketMessageSent::dispatch($ticket, $event);

        // Send Filament Database & Browser Push notifications
        try {
            if ($isAdminReply) {
                if ($ticket->partner) {
                    $customerUrl = \Webkul\Software\Filament\Customer\Resources\TicketResource::getUrl('view', ['record' => $ticket->id]);

                    Notification::make()
                        ->title("رد جديد على التذكرة #{$ticket->ticket_number}")
                        ->body('فريق الدعم: ' . strip_tags(Str::limit($data['content'] ?? '', 80)))
                        ->actions([
                            Action::make('view')
                                ->label('عرض التذكرة')
                                ->url($customerUrl),
                        ])
                        ->sendToDatabase($ticket->partner)
                        ->broadcast($ticket->partner);
                }
            } else {
                $admins = User::all();
                $adminUrl = \Webkul\Software\Filament\Admin\Resources\TicketResource::getUrl('view', ['record' => $ticket->id]);

                Notification::make()
                    ->title("رد جديد على التذكرة #{$ticket->ticket_number}")
                    ->body(($ticket->partner?->name ?? 'العميل') . ': ' . strip_tags(Str::limit($data['content'] ?? '', 80)))
                    ->actions([
                        Action::make('view')
                            ->label('عرض التذكرة')
                            ->url($adminUrl),
                    ])
                    ->sendToDatabase($admins)
                    ->broadcast($admins);
            }
        } catch (\Throwable) {}

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
     * Strip dangerous HTML tags and event-handler attributes to prevent XSS.
     */
    private function sanitizeHtml(string $content): string
    {
        $allowedTags = '<p><br><b><i><strong><em><u><s><ul><ol><li><a>'
            .'<h1><h2><h3><h4><h5><h6><blockquote><pre><code><span><div>'
            .'<table><thead><tbody><tr><th><td><img>';

        $content = strip_tags($content, $allowedTags);

        // Remove on* event-handler attributes (e.g. onclick, onerror)
        $content = preg_replace('/\s+on[a-z]+\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s>]*)/i', '', $content) ?? $content;

        // Remove javascript: protocol in href/src/action attributes
        $content = preg_replace('/\s+(href|src|action)\s*=\s*["\']?\s*javascript:[^"\'>\s]*/i', '', $content) ?? $content;

        return $content;
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
