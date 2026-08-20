<?php

namespace Webkul\Software\Livewire;

use Filament\Actions\Action;
use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Notifications\Notification;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;
use Webkul\Software\Models\Ticket;
use Webkul\Software\Services\TicketService;
use Filament\Forms\Components\ViewField;

class TicketConversationPanel extends Component implements HasActions, HasForms
{
    use InteractsWithActions, InteractsWithForms, WithFileUploads;

    public Ticket $ticket;

    /** 'admin' or 'customer' */
    public string $senderType = 'admin';

    public bool $canReply = true;

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

    public function replyAction(): Action
    {
        return Action::make('reply')
            ->label('Reply')
            ->icon('heroicon-o-chat-bubble-left-ellipsis')
            ->color('primary')
            ->modalHeading('Reply to Ticket #'.$this->ticket->ticket_number)
            ->modalWidth('4xl')
            ->form([
                // RichEditor::make('content')
                //     ->label('Message')
                //     ->required()
                //     ->extraAttributes(['style' => 'min-height: 280px;'])
                //     ->columnSpanFull()
                //     ->autofocus(),
                TextInput::make('content')
                    ->label('Message')
                    ->required()
                    ->columnSpanFull()
                    ->autofocus(),
                // السطر ده هو اللي هيظهر مسجل الصوت في البوب-أب
                ViewField::make('voice_note')
                    ->label('رسالة صوتية (اختياري)')
                    ->view('software::components.voice-recorder')
                    ->columnSpanFull(),
                FileUpload::make('attachments')
                    ->label('Attachments')
                    ->multiple()
                    ->disk('public')
                    ->directory('software/tickets')
                    ->maxSize(10240)
                    ->columnSpanFull(),
            ])
            ->action(function (array $data): void {
                /** @var TicketService $service */
                $service = app(TicketService::class);

                $attachments = $data['attachments'] ?? [];

                // معالجة الرسالة الصوتية لو موجودة
                if (!empty($data['voice_note'])) {
                    // استخراج كود الـ Base64 النقي بدون الهيدر (data:audio/webm;base64,...)
                    $audioData = substr($data['voice_note'], strpos($data['voice_note'], ',') + 1);
                    $audioDecoded = base64_decode($audioData);

                    // إنشاء اسم عشوائي للملف
                    $fileName = 'software/tickets/voice_' . time() . '_' . uniqid() . '.webm';
                    
                    // حفظ الملف في السيرفر
                    \Illuminate\Support\Facades\Storage::disk('public')->put($fileName, $audioDecoded);
                    
                    // إضافته لقائمة المرفقات عشان يتربط بالتيكت
                    $attachments[] = $fileName;
                }

                $eventData = [
                    'content'    => $data['content'],
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
                    $attachments // نمرر المرفقات بعد إضافة الفويس ليها
                );

                Notification::make()
                    ->title('Reply sent successfully')
                    ->success()
                    ->send();

                $this->ticket->refresh();
            })
            ->visible(fn (): bool => $this->canReply && $this->ticket->status->value !== 'closed');
    }

    public function render(): View
    {
        $events = $this->ticket->events()
            ->with(['user', 'partner', 'attachments'])
            ->latest()
            ->get();

        return view('software::livewire.ticket-conversation-panel', [
            'ticket' => $this->ticket->load(['partner', 'program', 'license', 'attachments']),
            'events' => $events,
        ]);
    }
}
