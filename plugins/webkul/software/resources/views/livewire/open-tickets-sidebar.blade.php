<div
    x-data="openTicketsSidebar()"
    x-init="init()"
>
    <div class="flex items-center gap-2 px-1 mb-3">
        <x-heroicon-o-ticket class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            Active Tickets
        </span>
        @if ($tickets->isNotEmpty())
            <span class="ml-auto inline-flex items-center justify-center w-5 h-5 rounded-full bg-gray-100 dark:bg-gray-700 text-[10px] font-bold text-gray-600 dark:text-gray-400">
                {{ $tickets->count() }}
            </span>
        @endif
    </div>

    <div class="space-y-1">
        @forelse ($tickets as $ticket)
            @php
                $isCurrent   = $ticket->id === $currentTicketId;
                $url         = \Webkul\Software\Filament\Admin\Resources\TicketResource::getUrl('view', ['record' => $ticket->id]);
                $statusLabel = match ($ticket->status) {
                    \Webkul\Software\Enums\TicketStatus::Open    => 'Open',
                    \Webkul\Software\Enums\TicketStatus::Pending => 'Pending',
                    default                                       => 'Closed',
                };
                $statusClass = match ($ticket->status) {
                    \Webkul\Software\Enums\TicketStatus::Open    => 'background-color: #dcfce7; color: #15803d;',
                    \Webkul\Software\Enums\TicketStatus::Pending => 'background-color: #fef9c3; color: #a16207;',
                    default                                       => 'background-color: #f3f4f6; color: #4b5563;',
                };
            @endphp

            <a
                href="{{ $url }}"
                wire:navigate
                class="ticket-nav-item"
                data-current="{{ $isCurrent ? 'true' : 'false' }}"
                data-unread="{{ ($ticket->is_unread_admin && ! $isCurrent) ? 'true' : 'false' }}"
            >
                {{-- Top row: ticket # + unread dot --}}
                <div class="flex items-center gap-1.5 mb-0.5">
                    <span
                        class="text-xs font-bold"
                        style="{{ $isCurrent ? 'color: var(--color-primary-600, #2563eb)' : 'color: #9ca3af' }}"
                    >#{{ $ticket->ticket_number }}</span>

                    <span class="ticket-unread-dot"></span>
                </div>

                {{-- Title --}}
                <p
                    class="text-sm font-medium truncate leading-snug"
                    style="{{ $isCurrent ? 'color: var(--color-primary-900, #1e3a5f)' : 'color: inherit' }}"
                >{{ $ticket->title }}</p>

                {{-- Customer name --}}
                <p class="text-xs truncate mt-0.5" style="color: #6b7280;">
                    {{ $ticket->partner?->name ?? '—' }}
                </p>

                {{-- Bottom row: status badge + time --}}
                <div class="flex items-center justify-between mt-1.5">
                    <span class="text-xs font-medium px-1.5 py-0.5 rounded-full" style="{{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                    <time class="text-xs" style="color: #9ca3af;">
                        {{ $ticket->updated_at->diffForHumans(null, true) }}
                    </time>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-10" style="color: #9ca3af;">
                <x-heroicon-o-check-circle class="w-8 h-8 mb-2 opacity-40" />
                <p class="text-xs">No active tickets</p>
            </div>
        @endforelse
    </div>
</div>

@once
@push('scripts')
<script type="module">
    import { initializeApp, getApps } from 'https://www.gstatic.com/firebasejs/11.6.0/firebase-app.js';
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

    function openTicketsSidebar() {
        return {
            _unsubscribers: [],
            init() {
                if (! firebaseConfig.databaseURL) { return; }

                // Reuse the same Firebase app if already initialized
                const existing = getApps().find(a => a.name === 'sidebar');
                const app = existing ?? initializeApp(firebaseConfig, 'sidebar');
                const db  = getDatabase(app);

                // Listen to the root tickets/ node for any update
                const path = ref(db, 'tickets');
                const unsub = onValue(path, () => {
                    this.$wire.$refresh();
                }, { onlyOnce: false });

                this._unsubscribers.push(unsub);
            },
            destroy() {
                this._unsubscribers.forEach(fn => fn());
            },
        };
    }

    window.openTicketsSidebar = openTicketsSidebar;
</script>
@endpush
@endonce
