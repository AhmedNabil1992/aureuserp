<x-filament-panels::page>
    @if($this->connectionFailed)
        @include('psmonitor::filament.customer.components.connection-failed')
    @else
        {{ $this->table }}
    @endif
</x-filament-panels::page>
