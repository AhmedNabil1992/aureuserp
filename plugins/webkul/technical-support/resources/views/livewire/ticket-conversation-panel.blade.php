<div
    class="ticket-chat-wrapper w-full flex flex-col rounded-2xl border border-slate-200 dark:border-gray-800 shadow-md bg-white dark:bg-gray-900 overflow-hidden"
    style="height: calc(100vh - 13rem); min-height: 540px; max-height: 840px;"
    x-data="ticketMessenger({
        ticketId: {{ $ticket->id }},
        senderType: '{{ $senderType }}',
        senderName: '{{ $senderType === 'admin' ? (Auth::user()?->name ?? 'الدعم الفني') : ($ticket->partner?->name ?? 'العميل') }}',
        canReply: {{ $canReply ? 'true' : 'false' }},
        isClosed: {{ $ticket->status->value === 'closed' ? 'true' : 'false' }}
    })"
    x-init="init()"
    @keydown.window.escape="closeImage(); showCannedDropdown = false;"
    @message-sent.window="scrollToBottom(true)"
    @canned-reply-applied.window="showCannedDropdown = false; $nextTick(() => { $refs.chatTextarea && $refs.chatTextarea.focus(); })"
>
    @php
        $langPrefix = $senderType === 'admin'
            ? 'technical-support::filament/admin/resources/ticket.chat'
            : 'technical-support::filament/customer/ticket.chat';

        $partnerName = $ticket->partner?->name ?? 'عميل';
        $partnerWords = preg_split('/\s+/u', trim($partnerName));
        if (count($partnerWords) >= 2 && !empty($partnerWords[0]) && !empty($partnerWords[1])) {
            $ticketInitials = mb_strtoupper(mb_substr($partnerWords[0], 0, 1, 'UTF-8') . mb_substr($partnerWords[1], 0, 1, 'UTF-8'), 'UTF-8');
        } else {
            $ticketInitials = mb_strtoupper(mb_substr($partnerName, 0, 2, 'UTF-8'), 'UTF-8');
        }
    @endphp

    {{-- Scoped CSS to strictly constrain image sizes and style audio players --}}
    <style>
        .ticket-chat-wrapper img:not(.ticket-chat-lightbox-img) {
            max-width: 120px !important;
            max-height: 90px !important;
            width: auto !important;
            height: auto !important;
            object-fit: cover !important;
            border-radius: 10px !important;
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
        .ticket-audio-player::-webkit-media-controls-enclosure {
            border-radius: 12px;
        }
    </style>

    {{-- ── 1. FIXED TOP HEADER BAR ─────────────────────────────────────── --}}
    <div class="px-5 py-3.5 bg-slate-50 dark:bg-gray-900/90 border-b border-slate-200 dark:border-gray-800 flex items-center justify-between gap-3 shrink-0 select-none">
        <div class="flex items-center gap-3 min-w-0">
            <div class="relative shrink-0">
                <div style="background-color: #2563eb !important; color: #ffffff !important; width: 40px; height: 40px; min-width: 40px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; border: 2px solid #ffffff !important; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
                    <span style="color: #ffffff !important; font-weight: 900 !important; font-size: 14px !important; line-height: 1 !important;">
                        {{ $ticketInitials }}
                    </span>
                </div>
                <span class="absolute bottom-0 right-0 w-3 h-3 rounded-full border-2 border-white dark:border-gray-900 {{ $ticket->status->value === 'closed' ? 'bg-gray-400' : 'bg-emerald-500' }}"></span>
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
                    <span>•</span>
                    <span class="font-bold {{ match($ticket->status) {
                        \Webkul\TechnicalSupport\Enums\TicketStatus::Open => 'text-emerald-600 dark:text-emerald-400',
                        \Webkul\TechnicalSupport\Enums\TicketStatus::Pending => 'text-amber-600 dark:text-amber-400',
                        default => 'text-slate-500'
                    } }}">
                        {{ $ticket->status->getLabel() }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Top Right Actions: Ticket Metadata & Close/Reopen Ticket Button --}}
        <div class="flex items-center gap-2.5">
            <div class="hidden sm:flex items-center gap-2 text-xs font-semibold">
                <span class="px-2.5 py-1 rounded-full bg-blue-50 text-blue-700 dark:bg-blue-950 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                    {{ $ticket->service_label }}
                </span>
                <span class="px-2.5 py-1 rounded-full bg-slate-100 text-slate-700 dark:bg-gray-800 dark:text-gray-300 border border-slate-200 dark:border-gray-700">
                    {{ $ticket->priority->getLabel() }}
                </span>
            </div>

            {{-- Close / Reopen Ticket Button --}}
            @if ($ticket->status->value !== 'closed')
                <button
                    type="button"
                    wire:click="closeTicket"
                    wire:confirm="هل أنت متأكد من إغلاق هذه التذكرة؟"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-red-50 hover:bg-red-100 dark:bg-red-950/50 dark:hover:bg-red-900/50 text-red-700 dark:text-red-300 text-xs font-bold border border-red-200 dark:border-red-800 shadow-2xs transition"
                    title="إغلاق التذكرة"
                >
                    <x-heroicon-o-lock-closed class="w-4 h-4" />
                    <span class="hidden sm:inline">إغلاق التذكرة</span>
                </button>
            @else
                <button
                    type="button"
                    wire:click="reopenTicket"
                    wire:confirm="هل تريد إعادة فتح هذه التذكرة؟"
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-950/50 dark:hover:bg-emerald-900/50 text-emerald-700 dark:text-emerald-300 text-xs font-bold border border-emerald-200 dark:border-emerald-800 shadow-2xs transition"
                    title="إعادة فتح التذكرة"
                >
                    <x-heroicon-o-arrow-path class="w-4 h-4" />
                    <span class="hidden sm:inline">إعادة فتح</span>
                </button>
            @endif
        </div>
    </div>

    {{-- ── 2. SCROLLABLE MESSAGES FEED ─────────────────────────────────── --}}
    <div
        id="ticket-messages-stream"
        x-ref="messagesFeed"
        class="flex-1 p-4 sm:p-6 overflow-y-auto space-y-4 bg-slate-100/60 dark:bg-gray-950/70 scroll-smooth"
        style="direction: ltr; background-image: radial-gradient(rgba(148, 163, 184, 0.15) 1px, transparent 1px); background-size: 20px 20px;"
    >
        @php
            $currentDate = null;
        @endphp

        @forelse ($events as $event)
            @php
                $eventDate = $event->created_at->translatedFormat('j F Y');
                $isAdminMessage = ! is_null($event->user_id);
                $isBotMessage   = is_null($event->user_id) && is_null($event->partner_id);
                $isMyMessage    = ! $isBotMessage && (
                                    ($senderType === 'admin' && $isAdminMessage)
                                    || ($senderType === 'customer' && ! $isAdminMessage)
                                );
                $isPrivateNote  = (bool) $event->is_private;
                $isSystemEvent  = $event->type === 'event';
                
                // Accurate name and avatar styling with 100% visible solid colors in Light and Dark mode
                if ($isBotMessage) {
                    $senderName = 'الرد الآلي للدعم';
                    $roleBadge  = '🤖 رد تلقائي';
                    $avatarStyle = 'background-color: #4f46e5 !important; color: #ffffff !important; border: 2px solid #a5b4fc !important;';
                    $initials   = '🤖';
                } elseif ($isAdminMessage) {
                    $senderName = $event->user?->name ?? 'فريق الدعم';
                    $roleBadge  = 'الدعم الفني';
                    $avatarStyle = 'background-color: #1e3a8a !important; color: #ffffff !important; border: 2px solid #93c5fd !important;';
                } else {
                    $senderName = $event->partner?->name ?? 'العميل';
                    $roleBadge  = 'العميل';
                    $avatarStyle = 'background-color: #047857 !important; color: #ffffff !important; border: 2px solid #6ee7b7 !important;';
                }

                if (! $isBotMessage) {
                    // Accurate UTF-8 multibyte initials
                    $words = preg_split('/\s+/u', trim($senderName));
                    if (count($words) >= 2 && !empty($words[0]) && !empty($words[1])) {
                        $initials = mb_strtoupper(mb_substr($words[0], 0, 1, 'UTF-8') . mb_substr($words[1], 0, 1, 'UTF-8'), 'UTF-8');
                    } else {
                        $initials = mb_strtoupper(mb_substr($senderName, 0, 2, 'UTF-8'), 'UTF-8');
                    }
                }
            @endphp

            {{-- Date Divider --}}
            @if ($currentDate !== $eventDate)
                @php $currentDate = $eventDate; @endphp
                <div class="flex items-center justify-center my-3 select-none" style="direction: rtl;">
                    <span class="px-3.5 py-1 text-xs font-bold text-slate-600 dark:text-gray-300 bg-white/95 dark:bg-gray-800/95 rounded-full border border-slate-200 dark:border-gray-700 shadow-2xs backdrop-blur-sm">
                        {{ $eventDate }}
                    </span>
                </div>
            @endif

            {{-- ── Message Item ───────────────────────────────────────── --}}
            @if ($isSystemEvent)
                {{-- System Event (e.g. Ticket Closed / Reopened) --}}
                <div class="flex justify-center my-2.5" style="direction: rtl;">
                    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-slate-200/90 dark:bg-gray-800/90 border border-slate-300 dark:border-gray-700 text-xs font-bold text-slate-700 dark:text-gray-300 shadow-2xs">
                        <x-heroicon-o-information-circle class="w-4 h-4 text-blue-500 shrink-0" />
                        <span>{{ $event->content }}</span>
                        <span class="text-[10px] font-mono text-slate-500">({{ $event->created_at->format('H:i') }})</span>
                    </div>
                </div>
            @elseif ($isPrivateNote)
                {{-- Staff Internal Note --}}
                <div class="flex justify-center my-3" style="direction: rtl;">
                    <div class="max-w-[95%] sm:max-w-[85%] w-full bg-amber-50 dark:bg-amber-950/40 border-2 border-amber-300/90 dark:border-amber-700/80 rounded-2xl p-4 shadow-sm">
                        <div class="flex items-center justify-between gap-2 pb-2 mb-2 border-b border-amber-200/80 dark:border-amber-800/60">
                            <div class="flex items-center gap-1.5 text-xs font-bold text-amber-900 dark:text-amber-300">
                                <x-heroicon-s-lock-closed class="w-4 h-4 text-amber-600 shrink-0" />
                                <span>{{ __("{$langPrefix}.internal_note_title") }}</span>
                                <span class="font-medium text-amber-800 dark:text-amber-400">({{ $senderName }})</span>
                            </div>
                            <span class="text-xs font-mono font-semibold text-amber-700 dark:text-amber-400">
                                {{ $event->created_at->format('H:i') }}
                            </span>
                        </div>
                        <div class="text-sm font-medium text-amber-950 dark:text-amber-100 whitespace-pre-wrap leading-relaxed">
                            {!! nl2br(e($event->content)) !!}
                        </div>
                    </div>
                </div>
            @else
                {{-- 
                    STRICT DIRECTIONALITY (LTR feed):
                    - My Messages ($isMyMessage) -> RIGHT (justify-end in LTR) with Royal Blue Gradient
                    - Other Party (! $isMyMessage) -> LEFT (justify-start in LTR) with Emerald Green Gradient
                --}}
                <div class="w-full flex items-end gap-2.5 my-2.5 {{ $isMyMessage ? 'justify-end' : 'justify-start' }}">
                    
                    {{-- Left side Avatar (for other party) --}}
                    @if (! $isMyMessage)
                        <div class="shrink-0 mb-1">
                            <div style="{{ $avatarStyle }} width: 36px; height: 36px; min-width: 36px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
                                <span style="color: #ffffff !important; font-weight: 900 !important; font-size: 13px !important; line-height: 1 !important;">
                                    {{ $initials }}
                                </span>
                            </div>
                        </div>
                    @endif

                    {{-- Bubble Wrapper with Generous Width --}}
                    <div class="flex flex-col w-auto max-w-[92%] sm:max-w-[82%] {{ $isMyMessage ? 'items-end' : 'items-start' }}" style="direction: rtl;">
                        {{-- Sender Name & Badge --}}
                        <div class="flex items-center gap-1.5 mb-1 px-1 text-xs">
                            <span class="font-bold text-slate-800 dark:text-gray-200">
                                {{ $senderName }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wide {{ $isAdminMessage ? 'bg-indigo-100 text-indigo-900 dark:bg-indigo-950 dark:text-indigo-300' : 'bg-emerald-100 text-emerald-900 dark:bg-emerald-950 dark:text-emerald-300' }}">
                                {{ $roleBadge }}
                            </span>
                        </div>

                        {{-- Bubble Card --}}
                        <div
                            class="relative px-4 sm:px-5 py-3 sm:py-3.5 shadow-md {{ $isMyMessage ? 'rounded-2xl rounded-tr-xs' : 'rounded-2xl rounded-tl-xs' }}"
                            style="min-width: 140px; {{ $isMyMessage ? 'background: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%) !important; color: #ffffff !important;' : ($isBotMessage ? 'background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%) !important; color: #ffffff !important;' : 'background: linear-gradient(135deg, #059669 0%, #047857 100%) !important; color: #ffffff !important;') }}"
                        >
                            {{-- Text Content --}}
                            @if (!empty(trim(strip_tags($event->content))))
                                @php
                                    $escapedContent = e($event->content);
                                    $formattedContent = preg_replace(
                                        '/(https?:\/\/[^\s<]+)/i',
                                        '<a href="$1" target="_blank" rel="noopener noreferrer" class="underline font-bold hover:opacity-90 break-all text-white bg-black/25 hover:bg-black/40 px-2 py-0.5 rounded-lg inline-flex items-center gap-1 my-0.5 shadow-2xs transition"><svg class="w-3.5 h-3.5 inline shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg> $1</a>',
                                        $escapedContent
                                    );
                                @endphp
                                <div
                                    class="text-sm sm:text-base font-medium leading-relaxed whitespace-pre-wrap select-text text-right"
                                    style="color: #ffffff !important;"
                                >
                                    {!! nl2br($formattedContent) !!}
                                </div>
                            @endif

                            {{-- Attachments Section --}}
                            @if ($event->attachments->isNotEmpty())
                                <div class="mt-2.5 pt-2 flex flex-col gap-2 border-t border-white/30">
                                    @foreach ($event->attachments as $att)
                                        @if ($att->isImage())
                                            <div class="mt-1">
                                                <button
                                                    type="button"
                                                    @click.prevent="openImage('{{ $att->url }}')"
                                                    class="block relative rounded-lg overflow-hidden group focus:outline-none ring-1 ring-black/10 shadow-2xs transition transform hover:scale-105"
                                                    title="اضغط للتكبير"
                                                >
                                                    <img
                                                        src="{{ $att->url }}"
                                                        alt="{{ $att->original_name }}"
                                                        class="w-24 h-20 sm:w-28 sm:h-24 object-cover rounded-lg"
                                                        loading="lazy"
                                                    />
                                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center text-white">
                                                        <x-heroicon-o-magnifying-glass-plus class="w-5 h-5" />
                                                    </div>
                                                </button>
                                            </div>
                                        @elseif ($att->isAudio())
                                            {{-- Spacious, Easy-to-use Audio Player --}}
                                            <div class="p-3 rounded-2xl w-full min-w-[250px] sm:min-w-[290px] bg-white/20 text-white">
                                                <div class="flex items-center justify-between gap-2 mb-1.5 text-xs font-bold">
                                                    <div class="flex items-center gap-1.5">
                                                        <x-heroicon-s-microphone class="w-4 h-4 shrink-0 text-white" />
                                                        <span>{{ __("{$langPrefix}.voice_message") }}</span>
                                                    </div>
                                                    <span class="text-[10px] opacity-80 font-mono">{{ $att->readable_size }}</span>
                                                </div>
                                                <audio controls class="ticket-audio-player w-full h-9 rounded-xl focus:outline-none shadow-2xs">
                                                    <source src="{{ $att->url }}" type="{{ $att->mime_type ?? 'audio/webm' }}">
                                                    {{ __("{$langPrefix}.browser_no_audio") }}
                                                </audio>
                                            </div>
                                        @else
                                            <a
                                                href="{{ $att->url }}"
                                                target="_blank"
                                                download="{{ $att->original_name }}"
                                                class="flex items-center gap-2.5 p-2.5 rounded-xl transition bg-white/20 hover:bg-white/30 text-white"
                                            >
                                                <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 bg-white/25 text-white">
                                                    <x-heroicon-o-paper-clip class="w-4 h-4" />
                                                </div>
                                                <div class="min-w-0 flex-1">
                                                    <p class="text-xs font-bold truncate">{{ $att->original_name }}</p>
                                                    <p class="text-[10px] opacity-80">{{ $att->readable_size }}</p>
                                                </div>
                                                <x-heroicon-o-arrow-down-tray class="w-4 h-4 opacity-75 shrink-0" />
                                            </a>
                                        @endif
                                    @endforeach
                                </div>
                            @endif

                            {{-- Time & Read Indicator --}}
                            <div class="flex items-center justify-end gap-1 mt-1 text-[11px] font-mono font-medium text-white/80">
                                <span>{{ $event->created_at->format('H:i') }}</span>
                                @if ($isMyMessage)
                                    <x-heroicon-s-check class="w-3.5 h-3.5 inline" />
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Right side Avatar (for my message) --}}
                    @if ($isMyMessage)
                        <div class="shrink-0 mb-1">
                            <div style="{{ $avatarStyle }} width: 36px; height: 36px; min-width: 36px; border-radius: 9999px; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
                                <span style="color: #ffffff !important; font-weight: 900 !important; font-size: 13px !important; line-height: 1 !important;">
                                    {{ $initials }}
                                </span>
                            </div>
                        </div>
                    @endif

                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center py-20 text-slate-400 dark:text-gray-500 select-none" style="direction: rtl;">
                <div class="w-16 h-16 rounded-full bg-white dark:bg-gray-800 border border-slate-200 dark:border-gray-700 flex items-center justify-center mb-3 shadow-xs">
                    <x-heroicon-o-chat-bubble-left-right class="w-8 h-8 text-slate-400" />
                </div>
                <p class="text-sm font-bold text-slate-700 dark:text-gray-300">{{ __("{$langPrefix}.no_messages") }}</p>
                <p class="text-xs text-slate-500 dark:text-gray-400 mt-1">{{ __("{$langPrefix}.start_conversation") }}</p>
            </div>
        @endforelse

        {{-- ── TYPING INDICATOR (MKS-SUPPORT-CHAT STYLE) ─────────────────── --}}
        <div x-show="otherPartyTyping" x-transition style="display: none; direction: rtl;" class="flex justify-start my-2">
            <div class="inline-flex items-center gap-2 rounded-2xl border border-slate-200 dark:border-gray-700 bg-white dark:bg-gray-800 px-4 py-2 text-xs font-semibold text-slate-600 dark:text-gray-300 shadow-xs">
                <span class="flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 0ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 150ms"></span>
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-bounce" style="animation-delay: 300ms"></span>
                </span>
                <span x-text="otherPartyTypingName + ' يكتب الآن...'"></span>
            </div>
        </div>
    </div>

    {{-- ── 3. PINNED BOTTOM COMPOSER (MESSENGER STYLE) ─────────────────── --}}
    @if ($this->canReply && $ticket->status->value !== 'closed')
        <div class="p-3 sm:p-4 bg-white dark:bg-gray-900 border-t border-slate-200 dark:border-gray-800 shrink-0 relative" style="direction: rtl;">
            {{-- Staff Private Note & Quick Replies Bar (Admin only) --}}
            <div class="flex items-center justify-between mb-2.5 px-1 text-xs">
                @if ($senderType === 'admin')
                    <div class="flex items-center gap-3">
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

                        {{-- Canned Replies Button (⚡ ردود سريعة) with UPWARD Popover --}}
                        @if ($cannedReplies->isNotEmpty())
                            <div class="relative" x-data="{ open: false }">
                                <button
                                    type="button"
                                    @click="open = !open"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-amber-500/10 to-indigo-500/10 hover:from-amber-500/20 hover:to-indigo-500/20 text-slate-800 dark:text-slate-200 font-bold transition shadow-xs border border-amber-300/80 dark:border-indigo-700/80"
                                >
                                    <x-heroicon-s-bolt class="w-4 h-4 text-amber-500" />
                                    <span>ردود سريعة</span>
                                </button>

                                {{-- Spacious Popover Menu strictly opening UPWARDS --}}
                                <div
                                    x-show="open"
                                    @click.away="open = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="transform opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="transform opacity-100 translate-y-0 scale-100"
                                    style="display: none; position: absolute; bottom: calc(100% + 10px); right: 0; z-index: 9999;"
                                    class="w-80 sm:w-96 bg-white dark:bg-gray-850 rounded-2xl shadow-2xl border-2 border-indigo-300 dark:border-indigo-700 p-3 max-h-72 overflow-y-auto"
                                >
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-gray-700">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-indigo-700 dark:text-indigo-400">
                                            <x-heroicon-s-bolt class="w-4 h-4 text-amber-500" />
                                            <span>اختر رداً سريعاً لإدراجه</span>
                                        </div>
                                        <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600 font-bold text-sm">&times;</button>
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach ($cannedReplies as $reply)
                                            <button
                                                type="button"
                                                wire:click="applyCannedReply({{ $reply->id }})"
                                                @click="open = false"
                                                class="w-full text-right p-2.5 rounded-xl hover:bg-indigo-50 dark:hover:bg-indigo-950/60 transition flex flex-col gap-1 group border border-slate-100 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-700"
                                            >
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-slate-900 dark:text-white text-xs group-hover:text-indigo-600 dark:group-hover:text-indigo-400">{{ $reply->title }}</span>
                                                    @if ($reply->shortcut)
                                                        <span class="text-[11px] font-mono font-bold bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-md border border-indigo-200 dark:border-indigo-800">{{ $reply->shortcut }}</span>
                                                    @endif
                                                </div>
                                                <p class="text-[11px] text-slate-600 dark:text-gray-300 line-clamp-2 leading-relaxed">{{ $reply->content }}</p>
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- Quick Downloads / Tools Button (📦 روابط وبرامج) --}}
                        @if (!empty($quickDownloads) && $quickDownloads->isNotEmpty())
                            <div class="relative" x-data="{ openTools: false }">
                                <button
                                    type="button"
                                    @click="openTools = !openTools"
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-gradient-to-r from-blue-500/10 to-teal-500/10 hover:from-blue-500/20 hover:to-teal-500/20 text-slate-800 dark:text-slate-200 font-bold transition shadow-xs border border-blue-300/80 dark:border-teal-700/80"
                                >
                                    <x-heroicon-s-arrow-down-tray class="w-4 h-4 text-blue-500" />
                                    <span>روابط وبرامج</span>
                                </button>

                                {{-- Spacious Popover Menu strictly opening UPWARDS for Tools --}}
                                <div
                                    x-show="openTools"
                                    @click.away="openTools = false"
                                    x-transition:enter="transition ease-out duration-150"
                                    x-transition:enter-start="transform opacity-0 translate-y-2 scale-95"
                                    x-transition:enter-end="transform opacity-100 translate-y-0 scale-100"
                                    style="display: none; position: absolute; bottom: calc(100% + 10px); right: 0; z-index: 9999;"
                                    class="w-80 sm:w-96 bg-white dark:bg-gray-850 rounded-2xl shadow-2xl border-2 border-blue-300 dark:border-blue-700 p-3 max-h-72 overflow-y-auto"
                                >
                                    <div class="flex items-center justify-between pb-2 mb-2 border-b border-slate-100 dark:border-gray-700">
                                        <div class="flex items-center gap-1.5 text-xs font-bold text-blue-700 dark:text-blue-400">
                                            <x-heroicon-s-arrow-down-tray class="w-4 h-4 text-blue-500" />
                                            <span>إرسال رابط برنامج / أداة مساعدة</span>
                                        </div>
                                        <button type="button" @click="openTools = false" class="text-slate-400 hover:text-slate-600 font-bold text-sm">&times;</button>
                                    </div>
                                    <div class="space-y-1.5">
                                        @foreach ($quickDownloads as $download)
                                            <button
                                                type="button"
                                                wire:click="insertQuickDownload({{ $download->id }})"
                                                @click="openTools = false"
                                                class="w-full text-right p-2.5 rounded-xl hover:bg-blue-50 dark:hover:bg-blue-950/60 transition flex flex-col gap-1 group border border-slate-100 dark:border-gray-700 hover:border-blue-300 dark:hover:border-blue-700"
                                            >
                                                <div class="flex items-center justify-between gap-2">
                                                    <span class="font-bold text-slate-900 dark:text-white text-xs group-hover:text-blue-600 dark:group-hover:text-blue-400">{{ $download->title }}</span>
                                                    @if ($download->version)
                                                        <span class="text-[10px] font-mono bg-blue-100 dark:bg-blue-950 text-blue-700 dark:text-blue-300 px-1.5 py-0.5 rounded">{{ $download->version }}</span>
                                                    @endif
                                                </div>
                                                @if ($download->description)
                                                    <p class="text-[11px] text-slate-600 dark:text-gray-300 line-clamp-1 leading-relaxed">{{ $download->description }}</p>
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    @if ($isPrivateNote)
                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-amber-100 text-amber-900 dark:bg-amber-950 dark:text-amber-300 border border-amber-300 dark:border-amber-800">
                            {{ __("{$langPrefix}.internal_note_active") }}
                        </span>
                    @endif
                @else
                    <div></div>
                @endif
            </div>

            {{-- File Previews --}}
            @if (!empty($files))
                <div class="flex flex-wrap gap-2 mb-2.5 p-2 bg-slate-100 dark:bg-gray-800/80 rounded-xl border border-slate-300 dark:border-gray-700">
                    @foreach ($files as $idx => $file)
                        <div class="flex items-center gap-1.5 px-3 py-1 rounded-lg bg-white dark:bg-gray-800 border border-slate-300 dark:border-gray-700 text-xs shadow-2xs">
                            <x-heroicon-o-paper-clip class="w-3.5 h-3.5 text-blue-600" />
                            <span class="truncate max-w-[140px] font-bold text-slate-800 dark:text-gray-200">{{ $file->getClientOriginalName() }}</span>
                            <button type="button" wire:click="removeUploadedFile({{ $idx }})" class="text-slate-400 hover:text-red-600 ml-1 font-bold text-sm">
                                &times;
                            </button>
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Voice Note Ready Preview --}}
            @if (!empty($voiceNoteData))
                <div class="flex items-center justify-between mb-2.5 p-2.5 bg-emerald-50 dark:bg-emerald-950/40 rounded-xl border-2 border-emerald-400 dark:border-emerald-700 text-xs text-emerald-950 dark:text-emerald-200 shadow-2xs">
                    <div class="flex items-center gap-2 font-medium">
                        <div class="w-7 h-7 rounded-full bg-emerald-600 text-white flex items-center justify-center shrink-0 shadow-sm">
                            <x-heroicon-s-microphone class="w-4 h-4 animate-pulse" />
                        </div>
                        <div>
                            <p class="font-bold text-emerald-900 dark:text-emerald-200">{{ __("{$langPrefix}.voice_recorded") }}</p>
                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400">جاهزة للإرسال مباشرة</p>
                        </div>
                    </div>
                    <button type="button" wire:click="clearVoiceNote" class="px-3 py-1 rounded-lg bg-red-100 hover:bg-red-200 text-red-700 text-xs font-bold transition">
                        {{ __("{$langPrefix}.cancel_recording") }}
                    </button>
                </div>
            @endif

            {{-- ── LIVE RECORDING BAR (ACTIVE RECORDING STATE) ─────────── --}}
            <div x-show="recording" style="display: none;" class="flex items-center justify-between gap-3 p-3 bg-red-50 dark:bg-red-950/50 rounded-xl border-2 border-red-400 dark:border-red-800 shadow-sm">
                <div class="flex items-center gap-3">
                    <div class="relative flex items-center justify-center w-8 h-8 rounded-full bg-red-600 text-white shadow-sm">
                        <span class="absolute w-full h-full rounded-full bg-red-400 animate-ping opacity-75"></span>
                        <x-heroicon-s-microphone class="w-4 h-4 relative z-10" />
                    </div>

                    <div class="flex items-center gap-2">
                        <span class="text-xs font-bold text-red-950 dark:text-red-200">جاري تسجيل الصوت...</span>
                        <span class="font-mono font-bold text-sm text-red-700 dark:text-red-300 px-2 py-0.5 rounded-md bg-white dark:bg-gray-900 border border-red-300 dark:border-red-800" x-text="formattedRecordTime">00:00</span>
                    </div>

                    {{-- Soundwave Equalizer Animation --}}
                    <div class="hidden sm:flex items-center gap-1 h-5">
                        <span class="w-1 h-3 bg-red-500 rounded-full animate-pulse"></span>
                        <span class="w-1 h-5 bg-red-600 rounded-full animate-bounce"></span>
                        <span class="w-1 h-2 bg-red-400 rounded-full animate-pulse"></span>
                        <span class="w-1 h-6 bg-red-600 rounded-full animate-bounce" style="animation-delay: 150ms"></span>
                        <span class="w-1 h-4 bg-red-500 rounded-full animate-pulse" style="animation-delay: 300ms"></span>
                    </div>
                </div>

                <div class="flex items-center gap-2">
                    <button
                        type="button"
                        @click="cancelVoiceRecording()"
                        class="px-3.5 py-1.5 rounded-lg bg-slate-200 hover:bg-slate-300 text-slate-800 text-xs font-bold transition"
                    >
                        إلغاء
                    </button>
                    <button
                        type="button"
                        @click="stopVoiceRecording()"
                        class="px-3.5 py-1.5 rounded-lg bg-red-600 hover:bg-red-700 text-white text-xs font-bold flex items-center gap-1.5 shadow-sm transition"
                    >
                        <x-heroicon-s-stop class="w-4 h-4" />
                        <span>إنهاء وحفظ</span>
                    </button>
                </div>
            </div>

            {{-- ── MAIN COMPOSER BAR (NOT RECORDING) ───────────────────── --}}
            <form x-show="!recording" wire:submit.prevent="sendInlineMessage" class="flex items-end gap-2.5">
                {{-- Attach File Button --}}
                <label
                    class="p-2.5 rounded-xl border border-slate-300 dark:border-gray-700 bg-slate-100 hover:bg-blue-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-slate-700 hover:text-blue-600 dark:text-gray-200 shadow-xs cursor-pointer transition shrink-0 flex items-center justify-center"
                    title="إرفاق ملفات أو صور"
                >
                    <x-heroicon-o-paper-clip class="w-5 h-5" />
                    <input type="file" wire:model="files" multiple class="hidden" />
                </label>

                {{-- Record Voice Note Button --}}
                <button
                    type="button"
                    @click="startVoiceRecording()"
                    class="p-2.5 rounded-xl border border-slate-300 dark:border-gray-700 bg-slate-100 hover:bg-emerald-50 dark:bg-gray-800 dark:hover:bg-gray-700 text-slate-700 hover:text-emerald-600 dark:text-gray-200 shadow-xs transition shrink-0 flex items-center justify-center"
                    title="{{ __("{$langPrefix}.record_voice") }}"
                >
                    <x-heroicon-s-microphone class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
                </button>

                {{-- Text Input with Auto-Grow and Typing Signal --}}
                <div class="flex-1 relative">
                    <textarea
                        x-ref="chatTextarea"
                        wire:model="message"
                        rows="1"
                        placeholder="{{ $isPrivateNote ? __("{$langPrefix}.placeholder_note") : __("{$langPrefix}.placeholder_message") }}"
                        @input="emitTyping(); autoResize($el);"
                        @keydown.enter.prevent="if(!$event.shiftKey) { $wire.sendInlineMessage(); }"
                        class="w-full resize-none rounded-xl border {{ $isPrivateNote ? 'border-amber-400 dark:border-amber-700 bg-amber-50/60 dark:bg-amber-950/30 text-amber-950 dark:text-amber-100' : 'border-slate-300 dark:border-gray-700 bg-white dark:bg-gray-800 text-slate-900 dark:text-slate-100' }} py-2.5 px-4 text-sm font-medium placeholder-slate-400 focus:bg-white dark:focus:bg-gray-900 focus:border-blue-500 focus:ring-1 focus:ring-blue-500 focus:outline-none transition max-h-48 sm:max-h-56 shadow-xs overflow-y-auto"
                    ></textarea>
                </div>

                {{-- Send Button (Blue / Amber) --}}
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="inline-flex items-center justify-center p-2.5 rounded-xl text-white font-bold shadow-sm transition shrink-0 {{ $isPrivateNote ? 'bg-amber-600 hover:bg-amber-700' : 'bg-blue-600 hover:bg-blue-700' }}"
                    title="إرسال"
                >
                    <span wire:loading.remove wire:target="sendInlineMessage">
                        <x-heroicon-m-paper-airplane class="w-5 h-5 rtl:rotate-180" />
                    </span>
                    <span wire:loading wire:target="sendInlineMessage">
                        <svg class="animate-spin h-5 w-5 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </span>
                </button>
            </form>
        </div>
    @else
        <div class="p-4 bg-slate-100 dark:bg-gray-800/80 border-t border-slate-200 dark:border-gray-800 flex items-center justify-between gap-3 shrink-0" style="direction: rtl;">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-600 dark:text-gray-400">
                <x-heroicon-s-lock-closed class="w-4 h-4 text-slate-500" />
                <span>{{ __("{$langPrefix}.ticket_closed_notice") }}</span>
            </div>

            <button
                type="button"
                wire:click="reopenTicket"
                class="px-3.5 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold transition shadow-sm flex items-center gap-1.5"
            >
                <x-heroicon-o-arrow-path class="w-4 h-4" />
                <span>إعادة فتح التذكرة</span>
            </button>
        </div>
    @endif

    {{-- ── 4. IMAGE LIGHTBOX POPUP ─────────────────────────────────────── --}}
    <div
        x-show="imageModalOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="ticket-chat-lightbox fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/85 backdrop-blur-sm"
        style="display:none;"
        @click.self="closeImage()"
    >
        <div class="relative max-w-4xl max-h-[90vh] bg-transparent flex flex-col items-center">
            <button
                type="button"
                @click="closeImage()"
                class="absolute -top-10 -right-2 text-white hover:text-gray-300 p-2 text-sm font-bold bg-black/40 rounded-lg focus:outline-none"
            >
                &times; {{ __("{$langPrefix}.close_lightbox") }}
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

                // Setup MutationObserver to automatically keep scrolled to bottom on any new messages
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

                    // Listen for typing events
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

            autoResize(el) {
                el.style.height = 'auto';
                el.style.height = Math.min(el.scrollHeight, 200) + 'px';
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