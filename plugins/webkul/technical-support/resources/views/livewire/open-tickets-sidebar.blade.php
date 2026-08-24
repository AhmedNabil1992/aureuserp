<div
    x-data="openTicketsSidebar()"
    x-init="init()"
>
    <div class="flex items-center gap-2 px-1 mb-3">
        <x-heroicon-o-ticket class="w-4 h-4 text-gray-400 dark:text-gray-500 shrink-0" />
        <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">
            {{ __('technical-support::filament/admin/resources/ticket.sidebar.active_tickets') }}
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
                $url         = route('filament.admin.resources.tickets.view', ['record' => $ticket->id]);
                $statusLabel = $ticket->status->getLabel();
                $statusClass = match ($ticket->status) {
                    \Webkul\TechnicalSupport\Enums\TicketStatus::Open    => 'background-color: #dcfce7; color: #15803d;',
                    \Webkul\TechnicalSupport\Enums\TicketStatus::Pending => 'background-color: #fef9c3; color: #a16207;',
                    default                                              => 'background-color: #f3f4f6; color: #4b5563;',
                };
            @endphp

            <a
                href="{{ $url }}"
                wire:navigate
                class="block p-3 rounded-lg border transition-colors mb-2 {{ $isCurrent ? 'bg-primary-50 border-primary-300 dark:bg-primary-950 dark:border-primary-700' : 'bg-white border-gray-200 hover:bg-gray-50 dark:bg-gray-900 dark:border-gray-800 dark:hover:bg-gray-800/60' }}"
            >
                <div class="flex items-center justify-between gap-1.5 mb-1">
                    <span class="text-xs font-bold {{ $isCurrent ? 'text-primary-600' : 'text-gray-500' }}">
                        #{{ $ticket->ticket_number }}
                    </span>

                    @if ($ticket->is_unread_admin && ! $isCurrent)
                        <span class="inline-flex items-center px-1.5 py-0.5 text-[10px] font-bold rounded-full bg-danger-500 text-white">
                            {{ __('technical-support::filament/admin/resources/ticket.sidebar.new_badge') }}
                        </span>
                    @endif
                </div>

                <p class="text-sm font-medium truncate leading-snug text-gray-900 dark:text-gray-100">
                    {{ $ticket->title }}
                </p>

                <div class="flex items-center justify-between text-xs text-gray-500 mt-1">
                    <span class="truncate max-w-[120px]">
                        {{ $ticket->partner?->name ?? '—' }}
                    </span>
                    <span class="font-medium px-1.5 py-0.5 rounded-full text-[10px]" style="{{ $statusClass }}">
                        {{ $statusLabel }}
                    </span>
                </div>
            </a>
        @empty
            <div class="flex flex-col items-center justify-center py-10 text-gray-400">
                <x-heroicon-o-check-circle class="w-8 h-8 mb-2 opacity-40" />
                <p class="text-xs">{{ __('technical-support::filament/admin/resources/ticket.sidebar.no_active_tickets') }}</p>
            </div>
        @endforelse
    </div>
</div>

@once
@push('scripts')
<script>
    function openTicketsSidebar() {
        return {
            init() {
                if (window.Echo) {
                    window.Echo.private('tickets.admin-sidebar')
                        .listen('.TicketMessageSent', () => {
                            this.$wire.$refresh();
                        });
                }
            },
        };
    }

    window.openTicketsSidebar = openTicketsSidebar;
</script>
@endpush
@endonce
