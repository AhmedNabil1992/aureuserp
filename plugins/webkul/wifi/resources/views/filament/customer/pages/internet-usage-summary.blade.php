<x-filament-panels::page>
    @php
        $counts = $this->getTypeCounts();
    @endphp

    <div class="flex justify-center mb-6">
        <x-filament::tabs>
            <!-- All Tab -->
            <x-filament::tabs.item
                :active="$activeTab === 'all'"
                wire:click="switchTab('all')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ __('wifi::filament/customer/pages/internet-usage-summary.tabs.all') }}</span>
                    <x-filament::badge color="gray">
                        {{ number_format($counts['all']) }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- Vouchers Tab -->
            <x-filament::tabs.item
                :active="$activeTab === 'voucher'"
                wire:click="switchTab('voucher')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ __('wifi::filament/customer/pages/internet-usage-summary.tabs.voucher') }}</span>
                    <x-filament::badge color="info">
                        {{ number_format($counts['voucher']) }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- Users Tab -->
            <x-filament::tabs.item
                :active="$activeTab === 'user'"
                wire:click="switchTab('user')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ __('wifi::filament/customer/pages/internet-usage-summary.tabs.user') }}</span>
                    <x-filament::badge color="warning">
                        {{ number_format($counts['user']) }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>
        </x-filament::tabs>
    </div>

    {{ $this->table }}
</x-filament-panels::page>
