{{-- Connection failed warning banner --}}
<div class="rounded-xl border border-warning-300 bg-warning-50 p-6 text-center dark:border-warning-600 dark:bg-warning-900/20">
    <div class="flex flex-col items-center gap-2">
        <x-filament::icon
            icon="heroicon-o-signal-slash"
            class="h-12 w-12 text-warning-500"
        />
        <h3 class="text-lg font-semibold text-warning-700 dark:text-warning-400">
            {{ __('psmonitor::filament/customer/pages/common.connection_failed.title') }}
        </h3>
        <p class="text-sm text-warning-600 dark:text-warning-400">
            {{ __('psmonitor::filament/customer/pages/common.connection_failed.body') }}
        </p>
    </div>
</div>
