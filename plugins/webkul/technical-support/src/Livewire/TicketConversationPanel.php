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
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
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

    protected function getListeners(): array
    {
        return [
            "echo:tickets.{$this->ticket->id},.TicketMessageSent" => '$refresh',
        ];
    }

    public function sendInlineMessage(): void
    {
        if (empty(trim($this->message)) && empty($this->files) && empty($this->voiceNoteData)) {
            return;
        }

        if (! $this->canReply || $this->ticket->status->value === 'closed') {
            return;
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
            
            // حماية إضافية: تأكيد أن العميل لا يمكنه أبداً إرسال رسالة خاصة حتى لو تلاعب بالـ Request
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

    public function removeUploadedFile(int $index): void
    {
        unset($this->files[$index]);
        $this->files = array_values($this->files);
    }

    public function clearVoiceNote(): void
    {
        $this->voiceNoteData = null;
    }

    public function replyAction(): Action
    {
        return Action::make('reply')
            ->label(__('technical-support::filament/resources/ticket.actions.reply'))
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('primary')
            ->modalHeading(__('technical-support::filament/resources/ticket.actions.reply_heading', ['number' => $this->ticket->ticket_number]))
            ->modalWidth('3xl')
            ->form([
                TextInput::make('content')
                    ->label(__('technical-support::filament/resources/ticket.form.fields.message'))
                    ->nullable()
                    ->columnSpanFull()
                    ->autofocus(),
                ViewField::make('voice_note')
                    ->label(__('technical-support::filament/resources/ticket.form.fields.voice_note'))
                    ->view('technical-support::components.voice-recorder')
                    ->columnSpanFull(),
                FileUpload::make('attachments')
                    ->label(__('technical-support::filament/resources/ticket.form.fields.attachments'))
                    ->multiple()
                    ->disk('public')
                    ->directory('technical-support/tickets')
                    ->maxSize(10240)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                $attachments = $data['attachments'] ?? [];
                $hasVoice = ! empty($data['voice_note']);
                $content = trim($data['content'] ?? '');

                if (empty($content) && empty($attachments) && ! $hasVoice) {
                    Notification::make()
                        ->title('يرجى كتابة رسالة أو تسجيل صوت أو إرفاق ملف للرد')
                        ->warning()
                        ->send();

                    return;
                }

                /** @var TicketService $service */
                $service = app(TicketService::class);

                if ($hasVoice) {
                    $audioData = substr($data['voice_note'], strpos($data['voice_note'], ',') + 1);
                    $audioDecoded = base64_decode($audioData);

                    $fileName = 'technical-support/tickets/voice_' . time() . '_' . uniqid() . '.webm';
                    Storage::disk('public')->put($fileName, $audioDecoded);
                    $attachments[] = $fileName;
                }

                $eventData = [
                    'content'    => $content ?: ($hasVoice ? '🎤 رسالة صوتية' : '📎 ملفات مرفقة'),
                    'type'       => 'message',
                    'is_private' => false,
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

                Notification::make()
                    ->title(__('technical-support::filament/resources/ticket.notifications.reply_sent'))
                    ->success()
                    ->send();

                $this->ticket->refresh();
            })
            ->visible(fn (): bool => $this->canReply && $this->ticket->status->value !== 'closed');
    }

    public function render(): View
    {
        // بناء الاستعلام الأساسي
        $query = $this->ticket->events()
            ->with(['user', 'partner', 'attachments'])
            ->oldest();

        // التعديل الأمني الأهم: 
        // لو اللي فاتح هو "العميل"، استبعد أي رسالة خاصة من قاعدة البيانات نهائياً
        // كده مستحيل توصل للمتصفح بتاعه لا في الـ HTML ولا في الـ JSON Response
        if ($this->senderType === 'customer') {
            $query->where('is_private', false);
        }

        $events = $query->get();

        return view('technical-support::livewire.ticket-conversation-panel', [
            'ticket' => $this->ticket->load(['partner', 'program', 'license', 'attachments']),
            'events' => $events,
        ]);
    }
}