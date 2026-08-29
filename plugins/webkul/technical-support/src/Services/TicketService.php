<?php

namespace Webkul\TechnicalSupport\Services;

use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Events\TicketMessageSent;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Models\TicketAttachment;
use Webkul\TechnicalSupport\Models\TicketEvent;
use Webkul\TechnicalSupport\Settings\SupportAutoReplySettings;

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

        $creatorUserId = $data['creator_id'] ?? null;
        $partnerId     = $data['partner_id'] ?? null;

        $ticket = Ticket::create($data);

        // 1. Create Initial Conversation Event (Problem Details)
        $initialContent = $data['content'] ?? $data['description'] ?? $ticket->title;
        if (empty(trim(strip_tags($initialContent)))) {
            $initialContent = !empty($filePaths) ? 'ملفات مرفقة / تسجيل صوتي' : ($ticket->title ?? 'تفاصيل المشكلة');
        }

        $initialEvent = $ticket->events()->create([
            'user_id'    => $creatorUserId,
            'partner_id' => $creatorUserId ? null : $partnerId,
            'type'       => 'message',
            'content'    => $initialContent,
            'is_private' => false,
        ]);

        if (! empty($filePaths)) {
            $this->saveAttachments($initialEvent, $filePaths);
        }

        // 2. Handle Auto-Reply System (Welcome / Off-Hours / Emergency Mode)
        // Only trigger auto-reply if created by a customer
        if (empty($creatorUserId) && ! empty($partnerId)) {
            $this->handleAutoReply($ticket);
        }

        // 3. Send notifications to assigned staff (Database + Email)
        try {
            $this->notificationService->notifyStaffNewTicket($ticket);
        } catch (\Throwable $e) {
            report($e);
        }

        // 4. Broadcast real-time Reverb event (non-blocking)
        try {
            TicketMessageSent::dispatch($ticket, $initialEvent);
        } catch (\Throwable $e) {
            // Ignored if WebSocket server is unreachable
        }

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

        // Auto-assign ticket to replying admin
        if (! empty($data['user_id'])) {
            $ticket->update([
                'assigned_to' => $data['user_id'],
            ]);
        }

        // Auto status updates
        if (! empty($data['user_id'])) {
            // Admin replied -> move to Pending if Open
            if ($ticket->status === TicketStatus::Open) {
                $ticket->update(['status' => TicketStatus::Pending]);
            }
        } elseif (! empty($data['partner_id'])) {
            // Customer replied -> move back to Open if Pending
            if ($ticket->status === TicketStatus::Pending) {
                $ticket->update(['status' => TicketStatus::Open]);
            }
        }

        $event = $ticket->events()->create($data);

        $this->saveAttachments($event, $filePaths);

        // Update ticket unread indicators & timestamps
        if (! empty($data['user_id']) && empty($data['is_private'])) {
            $ticket->update([
                'is_unread_client' => true,
                'last_replied_at'  => now(),
            ]);

            // Notify customer about admin reply (Database + Email)
            try {
                $this->notificationService->notifyCustomerNewReply($ticket, $event);
            } catch (\Throwable $e) {
                report($e);
            }
        } elseif (! empty($data['partner_id'])) {
            $ticket->update([
                'is_unread_admin' => true,
                'last_replied_at' => now(),
            ]);

            // Notify staff about customer reply (Database + Email)
            try {
                $this->notificationService->notifyStaffNewReply($ticket, $event);
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Real-time broadcast (non-blocking)
        try {
            TicketMessageSent::dispatch($ticket, $event);
        } catch (\Throwable $e) {
            // Ignored if WebSocket server is unreachable
        }

        return $event;
    }

    /**
     * Handle Automatic Replies (Welcome / Emergency / Business Hours).
     */
    protected function handleAutoReply(Ticket $ticket): void
    {
        try {
            /** @var SupportAutoReplySettings $settings */
            $settings = app(SupportAutoReplySettings::class);

            $autoMessage = null;

            // A. Emergency Mode Check (Highest Priority)
            if ($settings->is_emergency_mode) {
                $autoMessage = $settings->emergency_message ?: "نعتذر عن عدم توفر فريق الدعم حالياً لوجود ظرف طارئ، وسيتم مراجعة طلبك فور استئناف الخدمة.";
            } else {
                // B. Business Hours Check
                if ($settings->is_business_hours_enabled) {
                    $tz = $settings->timezone ?: 'Africa/Cairo';
                    $now = Carbon::now($tz);
                    $dayOfWeek = $now->dayOfWeek; // 0 (Sun) - 6 (Sat)
                    $currentTime = $now->format('H:i');

                    $workDays = $settings->work_days ?? [0, 1, 2, 3, 4];
                    $isWorkDay = in_array($dayOfWeek, $workDays);

                    $startTime = $settings->work_start_time ?: '09:00';
                    $endTime = $settings->work_end_time ?: '18:00';

                    $isWorkTime = ($currentTime >= $startTime && $currentTime <= $endTime);

                    if (! $isWorkDay || ! $isWorkTime) {
                        $autoMessage = $settings->out_of_hours_message ?: "نشكر تواصلك معنا. التذكرة مسجلة ولكنك تتواصل معنا خارج أوقات العمل الرسمية. سيتم الرد على استفسارك فور بدء موعد العمل القادم.";
                    } elseif ($settings->is_auto_reply_enabled && ! empty($settings->welcome_message)) {
                        $autoMessage = $settings->welcome_message;
                    }
                } elseif ($settings->is_auto_reply_enabled && ! empty($settings->welcome_message)) {
                    $autoMessage = $settings->welcome_message;
                }
            }

            if (! empty($autoMessage)) {
                $ticket->events()->create([
                    'user_id'    => null,
                    'partner_id' => null,
                    'type'       => 'message',
                    'content'    => "🤖 " . $autoMessage,
                    'is_private' => false,
                ]);
            }
        } catch (\Throwable $e) {
            report($e);
        }
    }

    /**
     * Close a ticket with system audit event.
     */
    public function closeTicket(Ticket $ticket, ?int $userId = null, ?int $partnerId = null): void
    {
        $ticket->update([
            'status' => TicketStatus::Closed,
        ]);

        $closedBy = $userId ? 'فريق الدعم الفني' : 'العميل';

        $event = $ticket->events()->create([
            'user_id'    => $userId,
            'partner_id' => $partnerId,
            'type'       => 'event',
            'content'    => "تم إغلاق التذكرة بواسطة {$closedBy}",
            'is_private' => false,
        ]);

        // Real-time broadcast
        TicketMessageSent::dispatch($ticket, $event);
    }

    /**
     * Reopen a closed ticket.
     */
    public function reopenTicket(Ticket $ticket, ?int $userId = null, ?int $partnerId = null): void
    {
        $ticket->update([
            'status' => TicketStatus::Open,
        ]);

        $reopenedBy = $userId ? 'فريق الدعم الفني' : 'العميل';

        $event = $ticket->events()->create([
            'user_id'    => $userId,
            'partner_id' => $partnerId,
            'type'       => 'event',
            'content'    => "تمت إعادة فتح التذكرة بواسطة {$reopenedBy}",
            'is_private' => false,
        ]);

        // Real-time broadcast
        TicketMessageSent::dispatch($ticket, $event);
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
                'file_name'     => $name,
                'original_name' => $name,
                'mime_type'     => $mime,
                'file_size'     => $size,
            ]);
        }
    }

    /**
     * Sanitize HTML content to prevent XSS.
     */
    protected function sanitizeHtml(string $html): string
    {
        return strip_tags($html, '<p><b><strong><i><em><u><a><ul><ol><li><br><span><code><pre><blockquote>');
    }
}
