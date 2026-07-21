<div class="fi-wi-license-selector rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4 mb-4">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap flex items-center gap-1.5">
            <x-heroicon-o-building-storefront class="w-5 h-5 text-primary-600 dark:text-primary-400 inline-block" />
            {{ __('psmonitor::filament/customer/widgets/license-selector.label') }}:
        </span>

        <select
            wire:change="selectLicense($event.target.value)"
            class="flex-1 min-w-[200px] rounded-lg border border-gray-300 bg-white px-3 py-2 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-700 dark:bg-gray-800 dark:text-white"
        >
            @foreach ($this->getLicenseOptions() as $id => $name)
                <option value="{{ $id }}" @selected($this->selected_license_id === $id)>
                    {{ $name }}
                </option>
            @endforeach
        </select>

        <div wire:loading wire:target="selectLicense" class="text-xs text-gray-500 dark:text-gray-400 flex items-center gap-1">
            <x-heroicon-o-arrow-path class="w-4 h-4 animate-spin text-primary-500 inline-block" />
            {{ __('psmonitor::filament/customer/widgets/license-selector.loading') }}
        </div>
    </div>
</div>
