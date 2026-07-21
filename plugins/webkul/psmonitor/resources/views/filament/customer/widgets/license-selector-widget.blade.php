<div class="fi-wi-license-selector fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-4">
    <div class="flex flex-wrap items-center gap-3">
        <span class="text-sm font-semibold text-gray-700 dark:text-gray-200 whitespace-nowrap">
            اختر المحل:
        </span>

        <select
            wire:change="selectLicense($event.target.value)"
            class="flex-1 min-w-[180px] rounded-lg border border-gray-300 bg-white px-3 py-1.5 text-sm text-gray-900 shadow-sm focus:border-primary-500 focus:outline-none focus:ring-1 focus:ring-primary-500 dark:border-gray-600 dark:bg-gray-800 dark:text-white"
        >
            @foreach ($this->getLicenseOptions() as $id => $name)
                <option value="{{ $id }}" @selected($this->selected_license_id === $id)>
                    {{ $name }}
                </option>
            @endforeach
        </select>

        <div wire:loading wire:target="selectLicense" class="text-xs text-gray-400">
            جاري التحميل...
        </div>
    </div>
</div>
