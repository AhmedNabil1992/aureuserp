<div
    class="ticket-conversation-panel"
    x-data="ticketConversation({{ $ticket->id }})"
    x-init="init()"
>

    {{-- Reply Action Button --}}
    @if ($this->canReply && $ticket->status->value !== 'closed')
        <div class="flex justify-end mb-6">
            {{ $this->replyAction }}
        </div>
    @endif

    {{-- Conversation Thread (newest first) — forced LTR so admin=right, customer=left --}}
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
            // Use Filament CSS variables so colors always resolve regardless of Tailwind compilation
            $avatarBg       = $isAdminMessage
                ? 'background-color: var(--color-primary-600, #4f46e5);'
                : 'background-color: var(--color-success-600, #16a34a);';
            $bubbleStyle    = $isMyMessage
                ? ($isAdminMessage
                    ? 'background-color: var(--color-primary-600, #4f46e5); color: #fff; border-bottom-right-radius: 4px;'
                    : 'background-color: var(--color-success-600, #16a34a); color: #fff; border-bottom-right-radius: 4px;')
                : 'background-color: #fff; border: 1px solid #e5e7eb; border-bottom-left-radius: 4px;';
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

                {{-- Sender name + badge (always visible, aligned to bubble side) --}}
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; padding: 0 4px; {{ $isMyMessage ? 'flex-direction: row-reverse;' : '' }}">
                    <span style="font-size:12px; font-weight:500; color:#4b5563;">{{ $senderName }}</span>
                    <span style="{{ $badgeStyle }}">{{ $badgeLabel }}</span>
                </div>

                {{-- Bubble --}}
                <div style="border-radius:16px; padding:14px 18px; box-shadow:0 1px 2px rgba(0,0,0,.06); {{ $bubbleStyle }}">

                    {{-- Content --}}
                    <div
                        class="prose prose-base max-w-none"
                        style="{{ $isMyMessage ? 'color:#fff;' : 'color:#1f2937;' }}"
                    >
                        {!! $event->content !!}
                    </div>

                    {{-- Attachments --}}
                    @if ($event->attachments->isNotEmpty())
                        <div style="margin-top:10px; padding-top:10px; display:flex; flex-wrap:wrap; gap:6px; {{ $attachBorderStyle }}">
                            @foreach ($event->attachments as $att)
                                <a
                                    href="{{ $att->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style="display:inline-flex; align-items:center; gap:4px; border-radius:8px; padding:4px 8px; font-size:11px; font-weight:500; text-decoration:none; transition:opacity .15s; {{ $attachLinkStyle }}"
                                >
                                    @if ($att->isImage())
                                        <x-heroicon-o-photo class="w-3.5 h-3.5 shrink-0" />
                                    @else
                                        <x-heroicon-o-paper-clip class="w-3.5 h-3.5 shrink-0" />
                                    @endif
                                    <span style="max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $att->original_name }}</span>
                                </a>
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
            <div style="flex:1;">
                <div style="display:flex; align-items:center; gap:6px; margin-bottom:4px; padding:0 4px;">
                    <span style="font-size:12px; font-weight:500; color:#4b5563;">{{ $creatorName }}</span>
                    <span style="background-color:#dcfce7; color:#15803d; font-size:10px; padding:1px 6px; border-radius:9999px; font-weight:500;">Customer</span>
                </div>
                <div style="border-radius:16px; border-bottom-left-radius:4px; background:#fff; border:1px solid #e5e7eb; padding:14px 18px; box-shadow:0 1px 2px rgba(0,0,0,.06);">
                    <div class="prose prose-base max-w-none" style="color:#1f2937;">
                        {!! $ticket->content !!}
                    </div>

                    @if ($ticket->attachments->isNotEmpty())
                        <div style="margin-top:10px; padding-top:10px; border-top:1px solid #e5e7eb; display:flex; flex-wrap:wrap; gap:6px;">
                            @foreach ($ticket->attachments as $att)
                                <a
                                    href="{{ $att->url }}"
                                    target="_blank"
                                    rel="noopener noreferrer"
                                    style="display:inline-flex; align-items:center; gap:4px; border-radius:8px; background:#f3f4f6; padding:4px 8px; font-size:11px; font-weight:500; color:#374151; text-decoration:none;"
                                >
                                    @if ($att->isImage())
                                        <x-heroicon-o-photo class="w-3.5 h-3.5 shrink-0" style="color:var(--color-primary-500,#6366f1)" />
                                    @else
                                        <x-heroicon-o-paper-clip class="w-3.5 h-3.5 shrink-0" />
                                    @endif
                                    <span style="max-width:130px; overflow:hidden; text-overflow:ellipsis; white-space:nowrap;">{{ $att->original_name }}</span>
                                </a>
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

    <x-filament-actions::modals />
</div>

@once
@push('scripts')
<script type="module">
    /**
     * Firebase Realtime Database listener for ticket conversations.
     * When the server writes to tickets/{id}/last_event, Livewire refreshes
     * the component — no polling required.
     *
     * Config values are injected from the server-side env via json_encode().
     */
    import { initializeApp } from 'https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js';
    import { getDatabase, ref, onValue } from 'https://www.gstatic.com/firebasejs/11.6.0/firebase-database.js';

    const firebaseConfig = {!! json_encode([
        'apiKey'            => config('services.firebase_web.api_key'),
        'authDomain'        => config('services.firebase_web.auth_domain'),
        'databaseURL'       => config('services.firebase_web.database_url'),
        'projectId'         => config('services.firebase_web.project_id'),
        'storageBucket'     => config('services.firebase_web.storage_bucket'),
        'messagingSenderId' => config('services.firebase_web.messaging_sender_id'),
        'appId'             => config('services.firebase_web.app_id'),
    ]) !!};

    function ticketConversation(ticketId) {
        return {
            _unsubscribe: null,
            init() {
                if (! firebaseConfig.databaseURL) { return; }

                const app = initializeApp(firebaseConfig, 'ticket-conversation-' + ticketId);
                const db  = getDatabase(app);
                const path = ref(db, 'tickets/' + ticketId + '/last_event');

                let lastEventId = null;

                this._unsubscribe = onValue(path, (snapshot) => {
                    const data = snapshot.val();
                    if (! data) { return; }

                    if (lastEventId !== null && data.event_id !== lastEventId) {
                        // New event arrived — ask Livewire to re-render
                        this.$wire.$refresh();
                    }
                    lastEventId = data.event_id;
                });
            },
            destroy() {
                if (this._unsubscribe) { this._unsubscribe(); }
            },
        };
    }

    window.ticketConversation = ticketConversation;
</script>
@endpush
@endonce
