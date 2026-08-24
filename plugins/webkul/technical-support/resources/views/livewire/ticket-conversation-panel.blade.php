<div
    class="ticket-chat-wrapper w-full flex flex-col rounded-2xl border border-slate-200 dark:border-gray-800 shadow-lg bg-white dark:bg-gray-900 overflow-hidden"
    style="height: calc(100vh - 13rem); min-height: 540px; max-height: 820px;"
    x-data="ticketMessenger({
        ticketId: {{ $ticket->id }},
        senderType: '{{ $senderType }}',
        senderName: '{{ $senderType === 'admin' ? (Auth::user()?->name ?? 'الدعم الفني') : ($ticket->partner?->name ?? 'العميل') }}',
        canReply: {{ $canReply ? 'true' : 'false' }},
        isClosed: {{ $ticket->status->value === 'closed' ? 'true' : 'false' }}
    })"
    x-init="init()"
    @keydown.window.escape="closeImage()"
    @message-sent.window="scrollToBottom(true)"
>
    @php
        $langPrefix = $senderType === 'admin'
            ? 'technical-support::filament/admin/resources/ticket.chat'
            : 'technical-support::filament/customer/ticket.chat';
    @endphp

    {{-- ── SCOPED CSS: FORCE STYLES TO BYPASS TAILWIND PURGE IN PLUGINS ── --}}
    <style>
        .ticket-chat-wrapper img:not(.ticket-chat-lightbox-img) {
            max-width: 140px !important;
            max-height: 100px !important;
            width: auto !important;
            height: auto !important;
            object-fit: cover !important;
            border-radius: 8px !important;
            display: inline-block !important;
            margin: 4px 0 !important;
            cursor: pointer !important;
            border: 1px solid rgba(0, 0, 0, 0.12) !important;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.08) !important;
            transition: transform 0.2s ease !important;
        }
        .ticket-chat-wrapper img:not(.ticket-chat-lightbox-img):hover {
            transform: scale(1.05) !important;
        }

        /* Chat Bubbles (Light Mode) */
        .chat-bubble-admin {
            background: linear-gradient(135deg, #2563eb, #1d4ed8) !important;
            color: #ffffff !important;
            border: 1px solid #1e40af !important;
        }
        .chat-bubble-customer {
            background: linear-gradient(135deg, #10b981, #059669) !important;
            color: #ffffff !important;
            border: 1px solid #047857 !important;
        }
        .chat-bubble-note {
            background: #fef3c7 !important;
            color: #92400e !important;
            border: 1px solid #fcd34d !important;
        }

        /* Chat Bubbles (Dark Mode) */
        .dark .chat-bubble-admin {
            background: linear-gradient(135deg, #1e40af, #1e3a8a) !important;
            border-color: #172554 !important;
        }
        .dark .chat-bubble-customer {
            background: linear-gradient(135deg, #047857, #064e3b) !important;
            border-color: #022c22 !important;
        }
        .dark .chat-bubble-note {
            background: #451a03 !important;
            color: #fde68a !important;
            border-color: #78350f !important;
        }

        /* Avatars */
        .chat-avatar-admin { background: linear-gradient(135deg, #2563eb, #4f46e5) !important; color: #fff !important; }
        .chat-avatar-customer { background: linear-gradient(135deg, #10b981, #059669) !important; color: #fff !important; }
        
        /* Send Button */
        .chat-btn-send { background: #2563eb !important; color: #ffffff !important; }
        .chat-btn-send:hover { background: #1d4ed8 !important; }
        .chat-btn-send.is-note { background: #d97706 !important; }
        .chat-btn-send.is-note:hover { background: #b45309 !important; }
    </style>

    {{-- ── 1. FIXED TOP HEADER BAR ─────────────────────────────────────── --}}
    <div class="px-4 sm:px-5 py-3.5 bg-slate-50 dark:bg-gray-900/90 border-b border-slate-200 dark:border-gray-800 flex items-center justify-between gap-3 shrink-0 select-none z-10 relative shadow-sm" style="direction: rtl;">
        <div class="flex items-center gap-3 min-w-0">
            <div class="relative shrink-0">
                <div class="w-10 h-10 rounded-full chat-avatar-customer flex items-center justify-center font-bold text-sm shadow-sm ring-2 ring-white dark:ring-gray-800">
                    {{ strtoupper(substr($ticket->partner?->name ?? 'T', 0, 2)) }}
                </div>
                <span class="absolute bottom-0 left-0 w-3.5 h-3.5 rounded-full border-2 border-white dark:border-gray-900 {{ $ticket->status->value === 'closed' ? 'bg-gray-400' : 'bg-emerald-500' }}"></span>
            </div>
            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white truncate">
                        {{ $ticket->title }}
                    </h3>
                    <span class="text-xs text-slate-500 dark:text-gray-400 font-mono font-bold bg-slate-200 dark:bg-gray-800 px-1.5 py-0.5 rounded">
                        #{{ $ticket->ticket_number }}
                    </span>
                </div>
                <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-gray-300 mt-0.5 font-medium">
                    <span>{{ $ticket->partner?->name ?? __("{$langPrefix}.customer") }}</span>
                    <span class="text-slate-300 dark:text-slate-600">•</span>
                    <span class="font-bold {{ match($ticket->status) {
                        \Webkul\TechnicalSupport\Enums\TicketStatus::Open => 'text-emerald-600 dark:text-emerald-400',
                        \Webkul\TechnicalSupport\Enums\TicketStatus::Pending => 'text-amber-600 dark:text-amber-400',
                        default => 'text-slate-500 dark:text-gray-400'
                    } }}">
                        {{ $ticket->status->getLabel() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Ticket Metadata Chips --}}
        <div class="hidden sm:flex items-center gap-2 text-xs font-semibold shrink-0">
            <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                {{ $ticket->service_label }}
            </span>
            <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-gray-800 dark:text-gray-300 border border-slate-200 dark:border-gray-700">
                {{ $ticket->priority->getLabel() }}
            </span>
        </div>
    </div>

    {{-- ── 2. SCROLLABLE MESSAGES FEED ─────────────────────────────────── --}}
    <div
        id="ticket-messages-stream"
        x-ref="messagesFeed"
        class="flex-1 p-3 sm:p-5 overflow-y-auto space-y-4 bg-slate-50/80 dark:bg-gray-950/70 scroll-smooth"
        {{-- تم تحويل الاتجاه الرئيسي هنا لـ ltr لضمان عمل الـ justify-end بشكل صحيح لوضع الرسالة يميناً --}}
        style="direction: ltr; background-image: radial-gradient(rgba(148, 163, 184, 0.15) 1px, transparent 1px); background-size: 20px 20px;"
    >
        @php
            $currentDate = null;
        @endphp

        @forelse ($events as $event)
            @php
                $isPrivateNote = (bool) $event->is_private;
            @endphp

            {{-- Security Check --}}
            @if ($isPrivateNote && $senderType === 'customer')
                @continue
            @endif

            @php
                $eventDate = $event->created_at->translatedFormat('j F Y');
                $isAdminMessage = ! is_null($event->user_id);
                // المتغير ده دلوقتي بيحدد بدقة: لو الرسالة بتاعتي (سواء أدمن أو عميل) = true
                $isMyMessage    = ($senderType === 'admin' && $isAdminMessage) || ($senderType === 'customer' && ! $isAdminMessage);
                $sender         = $isAdminMessage ? $event->user : $event->partner;
                $senderName     = $sender?->name ?? ($isAdminMessage ? __("{$langPrefix}.support_staff") : __("{$langPrefix}.customer"));
                $initials       = strtoupper(substr($senderName, 0, 2));

                $bubbleClass = $isPrivateNote ? 'chat-bubble-note' : ($isAdminMessage ? 'chat-bubble-admin' : 'chat-bubble-customer');
                $avatarClass = $isAdminMessage ? 'chat-avatar-admin' : 'chat-avatar-customer';
            @endphp

            {{-- Date Divider --}}
            @if ($currentDate !== $eventDate)
                @php $currentDate = $eventDate; @endphp
                <div class="flex items-center justify-center my-5 select-none" style="direction: rtl;">
                    <span class="px-4 py-1.5 text-[11px] font-bold text-slate-500 dark:text-gray-400 bg-white/95 dark:bg-gray-800/95 rounded-full border border-slate-200 dark:border-gray-700 shadow-sm backdrop-blur-sm">
                        {{ $eventDate }}
                    </span>
                </div>
            @endif

            {{-- ── Message Item ───────────────────────────────────────── --}}
            @if ($isPrivateNote)
                {{-- Staff Internal Note --}}
                <div class="flex justify-center my-3" style="direction: rtl;">
                    <div class="max-w-[95%] sm:max-w-[80%] w-full chat-bubble-note rounded-2xl p-3 sm:p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2 pb-2 mb-2 border-b border-amber-300/50 dark:border-amber-700/50">
                            <div class="flex items-center gap-1.5 text-xs font-bold">
                                <x-heroicon-s-lock-closed class="w-4 h-4 opacity-80 shrink-0" />
                                <span>{{ __("{$langPrefix}.internal_note_title") }}</span>
                                <span class="font-medium opacity-80">({{ $senderName }})</span>
                            </div>
                            <span class="text-xs font-mono font-semibold opacity-80">
                                {{ $event->created_at->format('H:i') }}
                            </span>
                        </div>
                        <div class="text-sm font-medium whitespace-pre-wrap leading-relaxed" dir="auto">
                            {!! nl2br(e($event->content)) !!}
                        </div>
                    </div>
                </div>
            @else
                {{-- 
                    Ordinary Messages (Chat Bubbles) 
                    - My Message: justify-end (Right in LTR)
                    - Other Message: justify-start (Left in LTR)
                --}}
                <div class="w-full flex {{ $isMyMessage ? 'justify-end' : 'justify-start' }} my-3">
                    
                    {{-- Message Row (Flex-row keeps DOM order: Left-to-Right) --}}
                    <div class="flex items-end gap-2 max-w-[92%] sm:max-w-[75%] flex-row">
                        
                        {{-- Avatar (for Other Party - Appears on Left) --}}
                        @if (! $isMyMessage)
                            <div class="shrink-0 mb-1">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold shadow-sm ring-1 ring-black/10 {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                            </div>
                        @endif

                        {{-- Bubble Content Wrapper (Forced RTL inside to keep Arabic Names/badges correct) --}}
                        <div class="flex flex-col {{ $isMyMessage ? 'items-end' : 'items-start' }}" style="direction: rtl;">
                            {{-- Sender Info --}}
                            <div class="flex items-center gap-1.5 mb-1 px-1 text-[10px] sm:text-xs">
                                <span class="font-bold text-slate-700 dark:text-gray-300">{{ $senderName }}</span>
                                <span class="px-1.5 py-0.5 rounded-[4px] font-bold uppercase tracking-wide {{ $isAdminMessage ? 'bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300' : 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/50 dark:text-emerald-300' }}">
                                    {{ $isAdminMessage ? __("{$langPrefix}.staff_badge") : __("{$langPrefix}.customer_badge") }}
                                </span>
                            </div>

                            {{-- Bubble Card --}}
                            <div class="relative px-3 sm:px-4 py-2 sm:py-3 shadow-md {{ $isMyMessage ? 'rounded-2xl rounded-br-sm' : 'rounded-2xl rounded-bl-sm' }} {{ $bubbleClass }}">
                                
                                {{-- Text Content --}}
                                @if (!empty(trim(strip_tags($event->content))))
                                    <div class="text-sm font-medium leading-relaxed whitespace-pre-wrap select-text" dir="auto">
                                        {!! nl2br(e($event->content)) !!}
                                    </div>
                                @endif

                                {{-- Attachments Section --}}
                                @if ($event->attachments->isNotEmpty())
                                    <div class="mt-2.5 pt-2 flex flex-col gap-2 border-t border-white/20">
                                        @foreach ($event->attachments as $att)
                                            @if ($att->isImage())
                                                <div class="mt-1">
                                                    <button
                                                        type="button"
                                                        @click.prevent="openImage('{{ $att->url }}')"
                                                        class="block relative rounded-lg overflow-hidden group focus:outline-none ring-1 ring-black/20 shadow-sm transition transform hover:scale-105"
                                                        title="اضغط للتكبير"
                                                    >
                                                        <img src="{{ $att->url }}" alt="{{ $att->original_name }}" loading="lazy" />
                                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                                            <x-heroicon-o-magnifying-glass-plus class="w-5 h-5" />
                                                        </div>
                                                    </button>
                                                </div>
                                            @elseif ($att->isAudio())
                                                <div class="p-2 sm:p-2.5 rounded-xl bg-black/15 text-white border border-white/10 shadow-inner">
                                                    <div class="flex items-center gap-2 mb-1 text-xs font-bold">
                                                        <x-heroicon-s-microphone class="w-4 h-4 shrink-0 text-white" />
                                                        <span>{{ __("{$langPrefix}.voice_message") }}</span>
                                                    </div>
                                                    <audio controls class="w-full h-8 max-w-[240px] rounded focus:outline-none opacity-90" dir="ltr">
                                                        <source src="{{ $att->url }}" type="{{ $att->mime_type ?? 'audio/webm' }}">
                                                    </audio>
                                                </div>
                                            @else
                                                <a
                                                    href="{{ $att->url }}"
                                                    target="_blank"
                                                    download="{{ $att->original_name }}"
                                                    class="flex items-center gap-2.5 p-2 rounded-xl transition bg-black/10 hover:bg-black/20 border border-white/10"
                                                >
                                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 bg-white/20 text-white">
                                                        <x-heroicon-o-paper-clip class="w-4 h-4" />
                                                    </div>
                                                    <div class="min-w-0 flex-1">
                                                        <p class="text-xs font-bold truncate text-white" dir="auto">{{ $att->original_name }}</p>
                                                        <p class="text-[10px] text-white/80">{{ $att->readable_size }}</p>
                                                    </div>
                                                    <x-heroicon-o-arrow-down-tray class="w-4 h-4 shrink-0 text-white/80" />
                                                </a>
                                            @endif
                                        @endforeach
                                    </div>
                                @endif

                                {{-- Time Indicator --}}
                                <div class="flex items-center justify-end gap-1 mt-1.5 text-[10px] sm:text-[11px] font-mono font-medium text-white/80">
                                    <span>{{ $event->created_at->format('H:i') }}</span>
                                    @if ($isMyMessage)
                                        <x-heroicon-s-check class="w-3.5 h-3.5 inline" />
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Avatar (For My Message - Appears on Right) --}}
                        @if ($isMyMessage)
                            <div class="shrink-0 mb-1">
                                <div class="w-7 h-7 sm:w-8 sm:h-8 rounded-full flex items-center justify-center text-[10px] sm:text-xs font-bold shadow-sm ring-1 ring-black/10 {{ $avatarClass }}">
                                    {{ $initials }}
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-slate-400 dark:text-gray-500 select-none" style="direction: rtl;">
                <div class="w-16 h-16 rounded-full bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 flex items-center justify-center mb-3 shadow-sm">
                    <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-slate-400" />
                </div>
                <p class="text-sm font-bold text-slate-700 dark:text-gray-300">{{ __("{$langPrefix}.no_messages") }}</p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">{{ __("{$langPrefix}.start_conversation") }}</p>
            </div>
        @endforelse

        {{-- ── TYPING INDICATOR ─────────────────── --}}
        <div x-show="otherPartyTyping" x-transition style="display: none;" class="flex justify-start my-2">
            <div class="inline-flex items-center gap-2 rounded-2xl rounded-bl-sm border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2.5 text-xs font-semibold text-slate-600 dark:text-gray-300 shadow-sm ml-8 sm:ml-10" style="direction: rtl;">
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-slate-400 animate-bounce" style="animation-delay: 300ms"></span>
                </span>
                <span x-text="otherPartyTypingName + ' يكتب الآن...'"></span>
            </div>
        </div>
    </div>

    {{-- ── 3. PINNED BOTTOM COMPOSER ─────────────────── --}}
    @if ($this->canReply && $ticket->status->value !== 'closed')
        <div class="p-2 sm:p-3 bg-white dark:bg-gray-900 border-t border-slate-200 dark:border-gray-800 shrink-0" style="direction: rtl;">
            {{-- Staff Private Note Toggle (Admin only) --}}
            @if ($senderType === 'admin')
                <div class="flex items-center justify-between mb-2.5 px-2 text-xs">
                    <label class="inline-flex items-center gap-2 cursor-pointer select-none text-slate-700 dark:text-gray-300">
                        <input
                            type="checkbox"
                            wire:model.live="isPrivateNote"
                            class="rounded text-amber-500 focus:ring-amber-400 h-4 w-4 border-slate-300 dark:border-gray-700"
                        />
                        <span class="flex items-center gap-1 font-bold {{ $isPrivateNote ? 'text-amber-600 dark:text-amber-400' : '' }}">
                            <x-heroicon-o-lock-closed class="w-3.5 h-3.5 text-amber-600" />
                            {{ __("{$langPrefix}.internal_note_toggle") }}
                        </span>
                    </label>

                    @if ($isPrivateNote)
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] sm:text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                            {{ __("{$langPrefix}.internal_note_active") }}
                        </span>
                    @endif
                </div>
            @endif

            {{-- File Previews --}}
            @if (!empty($files))
                <div class="flex flex-wrap gap-2 mb-2 p-2 bg-slate-100 dark:bg-gray-800/80 rounded-xl border border-slate-300 dark:border-gray-700">
                    @foreach ($files as $idx => $file)
                        <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-700 text-xs shadow-sm">
                            <x-heroicon-o-paper-clip class="w-3.5 h-3.5 text-blue-600" />
                            <span class="truncate max-w-[120px] sm:max-w-[140px] font-bold text-slate-800 dark:text-gray-200">{{ $file->getClientOriginalName() }}</span>
                            <button type="button" wire:click="removeUploadedFile({{ $idx }})" class="text-slate-400 hover:text-red-600 ml-1 font-bold text-lg leading-none">
                                &times;
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Voice Note Ready Preview --}}
            @if (!empty($voiceNoteData))
                <div class="flex items-center justify-between mb-2 p-2 sm:p-2.5 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border-2 border-emerald-400 dark:border-emerald-700 text-xs text-emerald-950 dark:text-emerald-200 shadow-sm">
                    <div class="flex items-center gap-2 font-medium">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <x-heroicon-s-microphone class="w-4 h-4 animate-pulse" />
                        </div>
                        <div>
                            <p class="font-bold text-emerald-900 dark:text-emerald-200">{{ __("{$langPrefix}.voice_recorded") }}</p>
                            <p class="text-[10px] sm:text-[11px] text-emerald-700 dark:text-emerald-400">جاهزة للإرسال مباشرة</p>
                        </div>
                    </div>
                    <button type="button" wire:click="clearVoiceNote" class="px-3 py-1.5 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-[10px] sm:text-xs font-bold transition">
                        {{ __("{$langPrefix}.cancel_recording") }}
                    </button>
                </div>
            @endif

            {{-- ── LIVE RECORDING BAR ─────────── --}}
            <div x-show="recording" style="display: none;" class="flex items-center justify-between gap-2 p-2 bg-red-50 dark:bg-red-950/50 rounded-full border border-red-200 dark:border-red-800 shadow-sm">
                <div class="flex items-center gap-2 px-2">
                    <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white shadow-sm shrink-0">
                        <span class="absolute w-full h-full rounded-full bg-red-400 animate-ping opacity-75"></span>
                        <x-heroicon-s-microphone class="w-4 h-4 relative z-10" />
                    </div>
                    <div class="flex flex-col sm:flex-row sm:items-center gap-1">
                        <span class="text-[10px] sm:text-xs font-bold text-red-900 dark:text-red-200 hidden sm:inline">جاري التسجيل...</span>
                        <span class="font-mono font-bold text-sm text-red-700 dark:text-red-300" x-text="formattedRecordTime">00:00</span>
                    </div>
                </div>

                <div class="flex items-center gap-1.5 pr-2">
                    <button type="button" @click="cancelVoiceRecording()" class="px-3 py-1.5 rounded-full bg-slate-200 hover:bg-slate-300 text-slate-800 text-[10px] sm:text-xs font-bold transition shrink-0">
                        إلغاء
                    </button>
                    <button type="button" @click="stopVoiceRecording()" class="px-3 py-1.5 rounded-full bg-red-600 hover:bg-red-700 text-white text-[10px] sm:text-xs font-bold flex items-center gap-1 shadow-sm transition shrink-0">
                        <x-heroicon-s-stop class="w-3.5 h-3.5" />
                        <span>حفظ</span>
                    </button>
                </div>
            </div>

            {{-- ── MAIN COMPOSER BAR ───────────────────── --}}
            <form x-show="!recording" wire:submit.prevent="sendInlineMessage" class="flex items-end gap-1.5 sm:gap-2 w-full bg-slate-100 dark:bg-gray-800/80 rounded-3xl p-1.5 border border-slate-200 dark:border-gray-700 focus-within:ring-1 focus-within:ring-blue-500 shadow-xs">
                
                {{-- Attach File Button --}}
                <div class="relative shrink-0">
                    <label
                        class="w-9 h-9 sm:w-10 sm:h-10 rounded-full hover:bg-slate-200 dark:hover:bg-gray-700 text-slate-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 cursor-pointer transition flex items-center justify-center"
                        title="إرفاق ملفات أو صور"
                        wire:loading.class="opacity-50 cursor-not-allowed pointer-events-none"
                        wire:target="files"
                    >
                        <x-heroicon-o-paper-clip class="w-5 h-5 sm:w-6 sm:h-6" wire:loading.remove wire:target="files" />
                        <svg wire:loading wire:target="files" class="animate-spin h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <input type="file" wire:model="files" multiple class="hidden" accept="image/*,.pdf,.doc,.docx,.zip,.rar" />
                    </label>
                </div>

                {{-- Record Voice Note Button --}}
                <button
                    type="button"
                    @click="startVoiceRecording()"
                    class="shrink-0 w-9 h-9 sm:w-10 sm:h-10 rounded-full hover:bg-slate-200 dark:hover:bg-gray-700 text-slate-500 dark:text-gray-400 hover:text-emerald-600 dark:hover:text-emerald-400 transition flex items-center justify-center"
                    title="{{ __("{$langPrefix}.record_voice") }}"
                >
                    <x-heroicon-s-microphone class="w-5 h-5 sm:w-6 sm:h-6" />
                </button>

                {{-- Text Input --}}
                <div class="flex-1 min-w-0" x-data="{ 
                    resize() { 
                        $el.querySelector('textarea').style.height = 'auto'; 
                        $el.querySelector('textarea').style.height = Math.min($el.querySelector('textarea').scrollHeight, 120) + 'px'; 
                    } 
                }">
                    <textarea
                        x-ref="messageInput"
                        wire:model="message"
                        rows="1"
                        dir="auto"
                        placeholder="{{ $isPrivateNote ? __("{$langPrefix}.placeholder_note") : __("{$langPrefix}.placeholder_message") }}"
                        @input="resize(); emitTyping()"
                        x-init="$watch('message', value => { if(!value) { $el.querySelector('textarea').style.height = '40px'; } })"
                        @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendInlineMessage(); $refs.messageInput.focus(); }"
                        style="min-height: 40px; max-height: 120px;"
                        class="w-full resize-none bg-transparent border-0 py-2.5 px-2 text-[13px] sm:text-sm font-medium placeholder-slate-400 focus:ring-0 text-slate-900 dark:text-slate-100 overflow-y-auto block"
                    ></textarea>
                </div>

                {{-- Send Button --}}
                <div class="shrink-0 pb-0.5 pl-0.5">
                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="chat-btn-send flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-full shadow-md transition {{ $isPrivateNote ? 'is-note' : '' }}"
                        title="إرسال"
                    >
                        <span wire:loading.remove wire:target="sendInlineMessage">
                            <x-heroicon-m-paper-airplane class="w-4 h-4 sm:w-5 sm:h-5 rtl:rotate-180 transform -translate-x-0.5" />
                        </span>
                        <span wire:loading wire:target="sendInlineMessage">
                            <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-3.5 bg-slate-100 dark:bg-gray-800 text-center text-xs font-bold text-slate-600 dark:text-gray-400 shrink-0" style="direction: rtl;">
            {{ __("{$langPrefix}.ticket_closed_notice") }}
        </div>
    @endif

    {{-- ── 4. IMAGE LIGHTBOX POPUP ─────────────────────────────────────── --}}
    <div
        x-show="imageModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-95"
        class="ticket-chat-lightbox fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm"
        style="display:none;"
        @click.self="closeImage()"
    >
        <div class="relative max-w-4xl w-full max-h-[90vh] bg-transparent flex flex-col items-center">
            <button
                type="button"
                @click="closeImage()"
                class="absolute -top-12 right-0 text-white hover:text-gray-300 p-2 text-sm font-bold bg-black/40 rounded-lg focus:outline-none transition"
            >
                &times; إغلاق
            </button>
            <img
                :src="activeImageUrl"
                class="ticket-chat-lightbox-img max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl"
            />
        </div>
    </div>
</div>

@once
@push('scripts')
<script>
    function ticketMessenger(config) {
        return {
            imageModalOpen: false,
            activeImageUrl: '',
            recording: false,
            mediaRecorder: null,
            audioChunks: [],
            recordSeconds: 0,
            timerInterval: null,
            observer: null,
            otherPartyTyping: false,
            otherPartyTypingName: '',
            typingTimeout: null,
            lastTypingEmit: 0,

            get formattedRecordTime() {
                let minutes = Math.floor(this.recordSeconds / 60);
                let seconds = this.recordSeconds % 60;
                return `${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
            },

            init() {
                this.scrollToBottom();

                const feed = this.$refs.messagesFeed;
                if (feed) {
                    this.observer = new MutationObserver(() => {
                        this.scrollToBottom();
                    });
                    this.observer.observe(feed, { childList: true, subtree: true });
                }

                if (window.Echo && config.ticketId) {
                    const channel = window.Echo.channel('tickets.' + config.ticketId);

                    channel.listen('.TicketMessageSent', () => {
                        this.otherPartyTyping = false;
                        this.$wire.$refresh().then(() => {
                            this.scrollToBottom(true);
                        });
                    });

                    channel.listenForWhisper('typing', (e) => {
                        if (e.senderType !== config.senderType) {
                            this.otherPartyTypingName = e.senderName || (config.senderType === 'admin' ? 'العميل' : 'الدعم الفني');
                            this.otherPartyTyping = true;
                            this.scrollToBottom(true);

                            clearTimeout(this.typingTimeout);
                            this.typingTimeout = setTimeout(() => {
                                this.otherPartyTyping = false;
                            }, 3000);
                        }
                    });
                }
            },

            emitTyping() {
                const now = Date.now();
                if (now - this.lastTypingEmit > 1500) {
                    this.lastTypingEmit = now;
                    if (window.Echo && config.ticketId) {
                        window.Echo.channel('tickets.' + config.ticketId)
                            .whisper('typing', {
                                senderType: config.senderType,
                                senderName: config.senderName
                            });
                    }
                }
            },

            async startVoiceRecording() {
                try {
                    const stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    this.audioChunks = [];
                    this.mediaRecorder = new MediaRecorder(stream);

                    this.mediaRecorder.ondataavailable = e => {
                        if (e.data.size > 0) {
                            this.audioChunks.push(e.data);
                        }
                    };

                    this.mediaRecorder.onstop = () => {
                        if (this.audioChunks.length > 0) {
                            let blob = new Blob(this.audioChunks, { type: 'audio/webm' });
                            let reader = new FileReader();
                            reader.readAsDataURL(blob);
                            reader.onloadend = () => {
                                this.$wire.set('voiceNoteData', reader.result);
                            };
                        }
                        stream.getTracks().forEach(track => track.stop());
                    };

                    this.mediaRecorder.start();
                    this.recording = true;
                    this.recordSeconds = 0;
                    this.timerInterval = setInterval(() => {
                        this.recordSeconds++;
                    }, 1000);
                } catch (err) {
                    alert('يرجى إعطاء صلاحية الوصول للمايكروفون لبدء التسجيل');
                }
            },

            stopVoiceRecording() {
                if (this.mediaRecorder && this.recording) {
                    this.mediaRecorder.stop();
                    this.recording = false;
                    clearInterval(this.timerInterval);
                }
            },

            cancelVoiceRecording() {
                if (this.mediaRecorder && this.recording) {
                    this.audioChunks = [];
                    this.mediaRecorder.stop();
                    this.recording = false;
                    clearInterval(this.timerInterval);
                }
            },

            scrollToBottom(smooth = false) {
                this.$nextTick(() => {
                    const feed = this.$refs.messagesFeed;
                    if (feed) {
                        feed.scrollTo({
                            top: feed.scrollHeight,
                            behavior: smooth ? 'smooth' : 'auto'
                        });
                    }
                });
            },

            openImage(url) {
                this.activeImageUrl = url;
                this.imageModalOpen = true;
            },

            closeImage() {
                this.imageModalOpen = false;
                this.activeImageUrl = '';
            }
        };
    }

    window.ticketMessenger = ticketMessenger;
</script>
@endpush
@endonce