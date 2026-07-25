<x-filament-panels::page>
    @php
        $counts = $this->getTabCounts();
    @endphp

    <div class="flex justify-center mb-6">
        <x-filament::tabs>
            <!-- Incomplete Tab -->
            <x-filament::tabs.item
                :active="$activeTab === 'incomplete'"
                wire:click="switchTab('incomplete')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ __('wifi::filament/customer/pages/voucher-invoices.tabs.incomplete') }}</span>
                    <x-filament::badge color="warning">
                        {{ number_format($counts['incomplete']) }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- All Tab -->
            <x-filament::tabs.item
                :active="$activeTab === 'all'"
                wire:click="switchTab('all')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ __('wifi::filament/customer/pages/voucher-invoices.tabs.all') }}</span>
                    <x-filament::badge color="gray">
                        {{ number_format($counts['all']) }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
