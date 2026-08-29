<?php

namespace Webkul\TechnicalSupport\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection as EloquentCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Webkul\TechnicalSupport\Enums\TicketStatus;
use Webkul\TechnicalSupport\Models\CannedReply;
use Webkul\TechnicalSupport\Models\QuickDownload;
use Webkul\TechnicalSupport\Models\Ticket;
use Webkul\TechnicalSupport\Services\TicketService;

class TicketConversationPanel extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, WithFileUploads;

    public Ticket $ticket;

    /** 'admin' or 'customer' */
    public string $senderType = 'admin';

    public bool $canReply = true;

    /** Inline message composer properties */
    public string $message = '';

    public array $files = [];

    public ?string $voiceNoteData = null;

    public bool $isPrivateNote = false;

    public function mount(Ticket $ticket, string $senderType = 'admin', bool $canReply = true): void
    {
        $this->ticket = $ticket;
        $this->senderType = $senderType;
        $this->canReply = $canReply;
    }

    public function getListeners(): array
    {
        $ticketId = isset($this->ticket) ? $this->ticket->id : null;

        if (! $ticketId) {
            return [];
        }

        return [
            "echo-private:tickets.{$ticketId},.TicketMessageSent" => '$refresh',
        ];
    }

    public function getCannedRepliesProperty(): EloquentCollection
    {
        return CannedReply::where('is_active', true)
            ->where(function ($q) {
                $q->whereNull('service_type')
                  ->orWhere('service_type', $this->ticket->service_type->value);
            })
            ->latest()
            ->get();
    }

    public function getQuickDownloadsProperty(): EloquentCollection
    {
        return QuickDownload::where('is_active', true)
            ->orderBy('sort_order')
            ->latest()
            ->get();
    }

    /**
     * Resolve a "/shortcut" command to its canned reply content.
     *
     * @return string|null The resolved content, or null when the message starts
     *                     with "/" but no matching shortcut exists.
     */
    protected function resolveSlashCommand(string $message): ?string
    {
        $trimmed = trim($message);

        if ($trimmed === '' || ! str_starts_with($trimmed, '/')) {
            return $trimmed;
        }

        // Extract the first token after "/", e.g. "/fixed" -> "fixed"
        $firstToken = preg_split('/\s+/u', $trimmed)[0] ?? '';
        $shortcutClean = strtolower(ltrim($firstToken, '/'));
        $shortcutWithSlash = '/' . $shortcutClean;

        $reply = CannedReply::where('is_active', true)
            ->where(function ($q) use ($shortcutClean, $shortcutWithSlash) {
                $q->whereRaw('LOWER(TRIM(shortcut)) = ?', [$shortcutClean])
                  ->orWhereRaw('LOWER(TRIM(shortcut)) = ?', [$shortcutWithSlash])
                  ->orWhereRaw("LOWER(TRIM(BOTH '/' FROM TRIM(shortcut))) = ?", [$shortcutClean]);
            })
            ->where(function ($q) {
                $q->whereNull('service_type')
                  ->orWhere('service_type', $this->ticket->service_type?->value ?? $this->ticket->service_type);
            })
            ->first();

        if (! $reply) {
            return null;
        }

        if ($trimmed === $firstToken) {
            return $reply->content;
        }

        $remainingText = trim(mb_substr($trimmed, mb_strlen($firstToken)));
        return $reply->content . ($remainingText !== '' ? "\n\n" . $remainingText : '');
    }

    public function applyCannedReply(int $id): void
    {
        $reply = CannedReply::find($id);
        if ($reply) {
            $this->message = $reply->content;
            $this->dispatch('canned-reply-applied');
        }
    }

    public function insertQuickDownload(int $id): void
    {
        $download = QuickDownload::find($id);
        if ($download) {
            $url = $download->download_url;
            $text = "📦 **{$download->title}**";
            if ($download->version) {
                $text .= " ({$download->version})";
            }
            if ($download->file_size) {
                $text .= " - الحجم: {$download->file_size}";
            }
            $text .= "\n🔗 رابط التحميل المباشر:\n{$url}";
            if ($download->description) {
                $text .= "\n{$download->description}";
            }

            $this->message = trim($this->message ? $this->message . "\n\n" . $text : $text);
            $this->dispatch('canned-reply-applied');
        }
    }

    public function sendInlineMessage(): void
    {
        if (empty(trim($this->message)) && empty($this->files) && empty($this->voiceNoteData)) {
            return;
        }

        if (! $this->canReply || $this->ticket->status === TicketStatus::Closed) {
            return;
        }

        // Admin only: resolve "/shortcut" commands to canned reply content
        if ($this->senderType === 'admin') {
            $resolved = $this->resolveSlashCommand($this->message);

            if (is_null($resolved)) {
                Notification::make()
                    ->title('لا يوجد رد سريع بهذا الاختصار')
                    ->body("لم يتم العثور على اختصار مطابق في الردود السريعة.")
                    ->warning()
                    ->send();

                return;
            }

            $this->message = $resolved;
        }


        /** @var TicketService $service */
        $service = app(TicketService::class);

        $attachments = [];

        // Save uploaded files
        if (! empty($this->files)) {
            foreach ($this->files as $file) {
                if ($file) {
                    $path = $file->store('technical-support/tickets', 'public');
                    $attachments[] = $path;
                }
            }
        }

        // Save voice note if present
        if (! empty($this->voiceNoteData)) {
            $audioData = substr($this->voiceNoteData, strpos($this->voiceNoteData, ',') + 1);
            $audioDecoded = base64_decode($audioData);

            $fileName = 'technical-support/tickets/voice_' . time() . '_' . uniqid() . '.webm';
            Storage::disk('public')->put($fileName, $audioDecoded);
            $attachments[] = $fileName;
        }

        $eventData = [
            'content'    => $this->message ?: ($this->voiceNoteData ? '🎤 رسالة صوتية' : 'ملفات مرفقة'),
            'type'       => $this->isPrivateNote && $this->senderType === 'admin' ? 'note' : 'message',
            'is_private' => $this->isPrivateNote && $this->senderType === 'admin',
        ];

        if ($this->senderType === 'admin') {
            $eventData['user_id'] = Auth::id();
        } else {
            $eventData['partner_id'] = Auth::guard('customer')->id();
        }

        $service->replyToTicket(
            $this->ticket,
            $eventData,
            $attachments
        );

        // Reset inputs
        $this->message = '';
        $this->files = [];
        $this->voiceNoteData = null;
        $this->isPrivateNote = false;

        $this->ticket->refresh();

        $this->dispatch('message-sent');
    }

    public function closeTicket(): void
    {
        /** @var TicketService $service */
        $service = app(TicketService::class);

        $userId = $this->senderType === 'admin' ? Auth::id() : null;
        $partnerId = $this->senderType === 'customer' ? Auth::guard('customer')->id() : null;

        $service->closeTicket($this->ticket, $userId, $partnerId);

        $this->ticket->refresh();

        Notification::make()
            ->title('تم إغلاق التذكرة بنجاح')
            ->success()
            ->send();

        $this->dispatch('message-sent');
    }

    public function reopenTicket(): void
    {
        /** @var TicketService $service */
        $service = app(TicketService::class);

        $userId = $this->senderType === 'admin' ? Auth::id() : null;
        $partnerId = $this->senderType === 'customer' ? Auth::guard('customer')->id() : null;

        $service->reopenTicket($this->ticket, $userId, $partnerId);

        $this->ticket->refresh();

        Notification::make()
            ->title('تمت إعادة فتح التذكرة بنجاح')
            ->success()
            ->send();

        $this->dispatch('message-sent');
    }

    public function reassignAction(): Action
    {
        return Action::make('reassign')
            ->label('إعادة تعيين')
            ->icon('heroicon-o-user-plus')
            ->color('gray')
            ->button()
            ->size('xs')
            ->modalHeading('إعادة تعيين التذكرة إلى مسؤول آخر')
            ->modalDescription('حدد الموظف أو الأدمن الذي تريد تحويل هذه التذكرة إليه.')
            ->modalSubmitActionLabel('حفظ التعيين')
            ->modalWidth('sm')
            ->fillForm(fn (): array => [
                'assigned_to' => $this->ticket->assigned_to,
            ])
            ->form([
                \Filament\Forms\Components\Select::make('assigned_to')
                    ->label('المسؤول')
                    ->options(\Webkul\Security\Models\User::pluck('name', 'id'))
                    ->searchable()
                    ->preload()
                    ->required(),
            ])
            ->action(function (array $data): void {
                $oldAssignee = $this->ticket->assignedTo?->name ?? 'غير مسندة';
                $this->ticket->update([
                    'assigned_to' => $data['assigned_to'],
                ]);

                $newAssignee = \Webkul\Security\Models\User::find($data['assigned_to']);

                $this->ticket->events()->create([
                    'user_id'    => Auth::id(),
                    'type'       => 'event',
                    'content'    => "تمت إعادة تعيين التذكرة من [{$oldAssignee}] إلى [{$newAssignee?->name}]",
                    'is_private' => true,
                ]);

                $this->ticket->refresh();

                Notification::make()
                    ->title('تمت إعادة تعيين التذكرة بنجاح')
                    ->body("المسؤول الحالي: {$newAssignee?->name}")
                    ->success()
                    ->send();

                $this->dispatch('message-sent');
            });
    }

    public function removeUploadedFile(int $index): void
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function clearVoiceNote(): void
    {
        $this->voiceNoteData = null;
    }

    public function render(): View
    {
        // Mark unread flags as read on view
        if ($this->senderType === 'admin' && $this->ticket->is_unread_admin) {
            $this->ticket->update(['is_unread_admin' => false]);
        } elseif ($this->senderType === 'customer' && $this->ticket->is_unread_client) {
            $this->ticket->update(['is_unread_client' => false]);
        }

        $events = $this->ticket->events()
            ->with(['attachments', 'user', 'partner'])
            ->when($this->senderType === 'customer', fn ($q) => $q->where('is_private', false))
            ->oldest()
            ->get();

        return view('technical-support::livewire.ticket-conversation-panel', [
            'events'         => $events,
            'cannedReplies'  => $this->cannedReplies,
            'quickDownloads' => $this->quickDownloads,
        ]);
    }
}