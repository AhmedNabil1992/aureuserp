<div
    class="ticket-conversation-panel relative"
    x-data="ticketConversation({{ $ticket->id }})"
    x-init="init()"
    @keydown.window.escape="closeImage()"
>

    {{-- Reply Action Button --}}
    @if ($this->canReply && $ticket->status->value !== 'closed')
        <div class="flex justify-end mb-6">
            {{ $this->replyAction }}
        </div>
    @endif

    {{-- Conversation Thread (newest first) --}}
    <div class="ticket-chat-ltr space-y-6">
    @forelse ($events as $event)
        @php
            $isAdminMessage = ! is_null($event->user_id);
            $isMyMessage    = ($senderType === 'admin' && $isAdminMessage)
                            || ($senderType === 'customer' && ! $isAdminMessage);
            $sender         = $isAdminMessage ? $event->user : $event->partner;
            $senderName     = $sender?->name ?? 'System';
            $initials       = strtoupper(substr($senderName, 0, 2));
            $badgeLabel     = $isAdminMessage ? 'Staff' : 'Customer';
            
            // Avatar Background
            $avatarBg       = $isAdminMessage
                ? 'background-color: var(--color-primary-600, #4f46e5);'
                : 'background-color: var(--color-success-600, #16a34a);';

            // Bubble Styling
            $bubbleStyle    = $isMyMessage
                ? ($isAdminMessage
                    ? 'background-color: var(--color-primary-600, #4f46e5); color: #fff; border-bottom-right-radius: 4px;'
                    : 'background-color: var(--color-success-600, #16a34a); color: #fff; border-bottom-right-radius: 4px;')
                : 'background-color: #fff; border: 1px solid #e5e7eb; border-bottom-left-radius: 4px;';

            // Badge Styling
            $badgeStyle     = $isAdminMessage
                ? 'background-color: #e0e7ff; color: #4338ca; font-size: 10px; padding: 1px 6px; border-radius: 9999px; font-weight: 500;'
                : 'background-color: #dcfce7; color: #15803d; font-size: 10px; padding: 1px 6px; border-radius: 9999px; font-weight: 500;';

            $attachBorderStyle = $isMyMessage ? 'border-top: 1px solid rgba(255,255,255,0.25);' : 'border-top: 1px solid #e5e7eb;';
            $attachLinkStyle   = $isMyMessage
                ? 'background: rgba(255,255,255,0.2); color: #fff;'
                : 'background: #f3f4f6; color: #374151;';
        @endphp

        <div style="display:flex; align-items:flex-end; gap:12px; {{ $isMyMessage ? 'flex-direction: row-reverse;' : 'flex-direction: row;' }}">

            {{-- Avatar --}}
            <div style="flex-shrink:0; margin-bottom:28px;">
                <div style="width:36px; height:36px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; box-shadow:0 1px 3px rgba(0,0,0,.12); {{ $avatarBg }}">
                    {{ $initials }}
                </div>
            </div>

            {{-- Bubble wrapper --}}
            <div style="max-width: 75%;">

                {{-- Sender name + badge --}}
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; padding: 0 4px; {{ $isMyMessage ? 'flex-direction: row-reverse;' : '' }}">
                    <span style="font-size:12px; font-weight:500; color:#4b5563;">{{ $senderName }}</span>
                    <span style="{{ $badgeStyle }}">{{ $badgeLabel }}</span>
                </div>

                {{-- Bubble --}}
                <div style="border-radius:16px; padding:14px 18px; box-shadow:0 1px 2px rgba(0,0,0,.06); {{ $bubbleStyle }}">

                    {{-- Content --}}
                    @if (!empty(trim(strip_tags($event->content))))
                        <div class="prose prose-base max-w-none" style="{{ $isMyMessage ? 'color:#fff;' : 'color:#1f2937;' }}">
                            {!! $event->content !!}
                        </div>
                    @endif

                    {{-- Attachments Section --}}
                    @if ($event->attachments->isNotEmpty())
                        <div style="margin-top:10px; padding-top:10px; display:flex; flex-direction:column; gap:8px; {{ $attachBorderStyle }}">
                            @foreach ($event->attachments as $att)
                                @php
                                    $ext = strtolower(pathinfo($att->original_name ?? $att->url, PATHINFO_EXTENSION));
                                    $isAudio = in_array($ext, ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'aac']);
                                @endphp

                                @if ($att->isImage())
                                    {{-- Image Preview (Opens in Popup) --}}
                                    <div class="mt-1">
                                        <button 
                                            type="button" 
                                            @click.prevent="openImage('{{ $att->url }}')" 
                                            class="inline-block transition-transform hover:scale-105 focus:outline-none"
                                        >
                                            <img
                                                src="{{ $att->url }}"
                                                alt="{{ $att->original_name }}"
                                                style="max-width:240px; max-height:240px; border-radius:10px; object-fit:cover; border: 1px solid {{ $isMyMessage ? 'rgba(255,255,255,0.3)' : '#e5e7eb' }}; box-shadow:0 2px 4px rgba(0,0,0,.08);"
                                                loading="lazy"
                                            />
                                        </button>
                                    </div>
                                @elseif ($isAudio)
                                    {{-- Audio Player --}}
                                    <div class="mt-2" style="width: 100%; min-width: 260px; max-width: 100%;">
                                        <audio controls src="{{ $att->url }}" style="width: 100%; height: 48px; border-radius: 8px; outline: none;"></audio>
                                    </div>
                                @else
                                    {{-- Standard File Attachment --}}
                                    <a
                                        href="{{ $att->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style="display:inline-flex; align-items:center; gap:6px; border-radius:8px; padding:6px 10px; font-size:11px; font-weight:500; text-decoration:none; align-self:flex-start; {{ $attachLinkStyle }}"
                                    >
                                        <x-heroicon-o-paper-clip class="w-4 h-4 shrink-0" />
                                        <span style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $att->original_name }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- Timestamp --}}
                <div style="margin-top:4px; padding: 0 4px; font-size:11px; color:#9ca3af; {{ $isMyMessage ? 'text-align:right;' : 'text-align:left;' }}">
                    {{ $event->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    @empty
        <div class="flex flex-col items-center justify-center py-16 text-gray-400 dark:text-gray-500" dir="ltr">
            <x-heroicon-o-chat-bubble-left-right class="w-12 h-12 mb-3 opacity-40" />
            <p class="text-sm">No replies yet</p>
        </div>
    @endforelse
    </div>

    {{-- Original Ticket Message --}}
    <div class="ticket-chat-ltr mt-8 pt-6 border-t border-dashed border-gray-200 dark:border-gray-700">
        <div class="flex items-center gap-2 mb-4">
            <x-heroicon-o-inbox class="w-4 h-4 text-gray-400" />
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                Original Request
            </span>
        </div>

        @php
            $creatorName = $ticket->partner?->name ?? $ticket->creator?->name ?? 'Unknown';
            $creatorInit = strtoupper(substr($creatorName, 0, 2));
        @endphp

        <div style="display:flex; align-items:flex-end; gap:12px;">
            <div style="flex-shrink:0; margin-bottom:28px;">
                <div style="width:36px; height:36px; border-radius:50%; background:#6b7280; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; box-shadow:0 1px 3px rgba(0,0,0,.12);">
                    {{ $creatorInit }}
                </div>
            </div>
            <div style="flex:1; max-width: 75%;">
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; padding:0 4px;">
                    <span style="font-size:12px; font-weight:500; color:#4b5563;">{{ $creatorName }}</span>
                    <span style="background-color:#dcfce7; color:#15803d; font-size:10px; padding:1px 6px; border-radius:9999px; font-weight:500;">Customer</span>
                </div>
                <div style="border-radius:16px; border-bottom-left-radius:4px; background:#fff; border:1px solid #e5e7eb; padding:14px 18px; box-shadow:0 1px 2px rgba(0,0,0,.06);">
                    <div class="prose prose-base max-w-none" style="color:#1f2937;">
                        {!! $ticket->content !!}
                    </div>

                    @if ($ticket->attachments->isNotEmpty())
                        <div style="margin-top:10px; padding-top:10px; border-top:1px solid #e5e7eb; display:flex; flex-direction:column; gap:8px;">
                            @foreach ($ticket->attachments as $att)
                                @php
                                    $ext = strtolower(pathinfo($att->original_name ?? $att->url, PATHINFO_EXTENSION));
                                    $isAudio = in_array($ext, ['webm', 'mp3', 'wav', 'ogg', 'm4a', 'aac']);
                                @endphp

                                @if ($att->isImage())
                                    <div class="mt-1">
                                        <button 
                                            type="button" 
                                            @click.prevent="openImage('{{ $att->url }}')" 
                                            class="inline-block transition-transform hover:scale-105 focus:outline-none"
                                        >
                                            <img
                                                src="{{ $att->url }}"
                                                alt="{{ $att->original_name }}"
                                                style="max-width:240px; max-height:240px; border-radius:10px; object-fit:cover; border:1px solid #e5e7eb; box-shadow:0 2px 4px rgba(0,0,0,.08);"
                                                loading="lazy"
                                            />
                                        </button>
                                    </div>
                                @elseif ($isAudio)
                                    <div class="mt-2" style="width: 100%; min-width: 260px; max-width: 100%;">
                                        <audio controls src="{{ $att->url }}" style="width: 100%; height: 48px; border-radius: 8px; outline: none;"></audio>
                                    </div>
                                @else
                                    <a
                                        href="{{ $att->url }}"
                                        target="_blank"
                                        rel="noopener noreferrer"
                                        style="display:inline-flex; align-items:center; gap:6px; border-radius:8px; background:#f3f4f6; padding:6px 10px; font-size:11px; font-weight:500; color:#374151; text-decoration:none; align-self:flex-start;"
                                    >
                                        <x-heroicon-o-paper-clip class="w-4 h-4 shrink-0" />
                                        <span style="max-width:160px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $att->original_name }}</span>
                                    </a>
                                @endif
                            @endforeach
                        </div>
                    @endif
                </div>
                <div style="margin-top:4px; padding:0 4px; font-size:11px; color:#9ca3af;">
                    {{ $ticket->created_at->diffForHumans() }}
                </div>
            </div>
        </div>
    </div>

    {{-- Fullscreen Image Modal (Popup) --}}
    <div 
        x-show="isImageOpen" 
        style="display: none;"
        @click="closeImage()" 
        class="fixed inset-0 z-[9999] flex items-center justify-center p-4 md:p-8 bg-gray-900/90 backdrop-blur-sm cursor-zoom-out"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0 scale-90"
        x-transition:enter-end="opacity-100 scale-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100 scale-100"
        x-transition:leave-end="opacity-0 scale-90"
    >
        {{-- Close Button --}}
        <button 
            type="button"
            @click.stop="closeImage()" 
            class="absolute top-4 right-4 md:top-6 md:right-6 text-white hover:text-gray-300 focus:outline-none transition-colors"
        >
            <x-heroicon-o-x-mark class="w-8 h-8 md:w-10 md:h-10" />
        </button>

        {{-- The Image --}}
        <img 
            :src="currentImageUrl" 
            @click.stop
            class="max-w-[95vw] max-h-[90vh] rounded-lg shadow-2xl object-contain cursor-default"
        />
    </div>

    <x-filament-actions::modals />
</div>

@once
@push('scripts')
<script>
    function ticketConversation(ticketId) {
        return {
            _unsubscribe: null,
            // المتغيرات الخاصة بالبوب-أب
            isImageOpen: false,
            currentImageUrl: '',
            
            // دالة فتح الصورة
            openImage(url) {
                this.currentImageUrl = url;
                this.isImageOpen = true;
                document.body.style.overflow = 'hidden'; // منع السكرول في الصفحة الخلفية
            },
            
            // دالة غلق الصورة
            closeImage() {
                this.isImageOpen = false;
                setTimeout(() => { this.currentImageUrl = ''; }, 300); // تفريغ الرابط بعد انتهاء الأنيميشن
                document.body.style.overflow = ''; // إرجاع السكرول
            },

            init() {
                // Listen to Reverb broadcast channel
                if (window.Echo) {
                    window.Echo.channel('tickets.' + ticketId)
                        .listen('.TicketMessageSent', () => {
                            this.$wire.$refresh();
                        });
                }

                // Fallback / secondary Firebase RTDB listener
                if (window.AureusFirebase?.hasRequiredFirebaseConfig && window.AureusFirebase?.firebaseConfig?.databaseURL) {
                    const db = window.AureusFirebase?.getDatabase('ticket-conversation-' + ticketId);
                    if (db && window.firebaseDatabase) {
                        const path = window.firebaseDatabase.ref(db, 'tickets/' + ticketId + '/last_event');
                        let lastEventId = null;
                        this._unsubscribe = window.firebaseDatabase.onValue(path, (snapshot) => {
                            const data = snapshot.val();
                            if (! data) { return; }
                            if (lastEventId !== null && data.event_id !== lastEventId) {
                                this.$wire.$refresh();
                            }
                            lastEventId = data.event_id;
                        });
                    }
                }
            },
            destroy() {
                if (this._unsubscribe) { this._unsubscribe(); }
            }
        };
    }

    window.ticketConversation = ticketConversation;
</script>
@endpush
@endonce