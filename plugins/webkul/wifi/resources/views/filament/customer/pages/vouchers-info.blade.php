<x-filament-panels::page>
    @php
        $counts = $this->getStatusCounts();
    @endphp

    <!-- شريط التبويبات باستخدام كامبوننت الـ Badges الرسمية لـ Filament -->
    <div class="flex justify-center mb-6">
        <x-filament::tabs>
            
            <!-- تبويب: الكل -->
            <x-filament::tabs.item
                :active="$activeTab === 'all'"
                wire:click="switchTab('all')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ $this->getLabel('all') }}</span>
                    <x-filament::badge color="gray">
                        {{ $counts['all'] }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- تبويب: جديد -->
            <x-filament::tabs.item
                :active="$activeTab === 'new'"
                wire:click="switchTab('new')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ $this->getLabel('new') }}</span>
                    <x-filament::badge color="success">
                        {{ $counts['new'] }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- تبويب: مستخدم -->
            <x-filament::tabs.item
                :active="$activeTab === 'used'"
                wire:click="switchTab('used')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ $this->getLabel('used') }}</span>
                    <x-filament::badge color="warning">
                        {{ $counts['used'] }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- تبويب: مستنفذ -->
            <x-filament::tabs.item
                :active="$activeTab === 'depleted'"
                wire:click="switchTab('depleted')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ $this->getLabel('depleted') }}</span>
                    <x-filament::badge color="info">
                        {{ $counts['depleted'] }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

            <!-- تبويب: منتهي -->
            <x-filament::tabs.item
                :active="$activeTab === 'expired'"
                wire:click="switchTab('expired')"
                class="px-4 py-2"
            >
                <div class="flex items-center gap-x-2">
                    <span class="font-medium">{{ $this->getLabel('expired') }}</span>
                    <x-filament::badge color="danger">
                        {{ $counts['expired'] }}
                    </x-filament::badge>
                </div>
            </x-filament::tabs.item>

        </x-filament::tabs>
    </div>

    <!-- جدول عرض الفاوتشرات -->
    {{ $this->table }}
</x-filament-panels::page>