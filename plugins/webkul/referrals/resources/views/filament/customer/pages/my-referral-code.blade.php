<x-filament-panels::page>
    <div class="grid gap-6 md:grid-cols-3">
        <div class="md:col-span-2 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm dark:border-gray-700 dark:bg-gray-900">
            <p class="text-sm text-gray-500 dark:text-gray-400">{{ __('referrals::app.customer.share_help') }}</p>
            <div class="mt-4 flex flex-col gap-3 sm:flex-row sm:items-center">
                <code class="flex-1 rounded-xl bg-gray-100 px-5 py-4 text-center text-2xl font-bold tracking-widest text-primary-700 dark:bg-gray-800 dark:text-primary-300">{{ $referralCode?->code }}</code>
                <x-filament::button
                    icon="heroicon-o-clipboard"
                    x-on:click="navigator.clipboard.writeText(@js($referralCode?->code)); $tooltip(@js(__('referrals::app.customer.copied')), { timeout: 2000 })"
                >
                    {{ __('referrals::app.customer.copy') }}
                </x-filament::button>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-success-200 bg-success-50 p-5 dark:border-success-500/20 dark:bg-success-500/10">
                <p class="text-sm text-success-700 dark:text-success-300">{{ __('referrals::app.customer.earned') }}</p>
                <p class="mt-1 text-2xl font-bold text-success-800 dark:text-success-200">{{ number_format($this->earnedTotal, 2) }}</p>
            </div>
            <div class="rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-500/20 dark:bg-warning-500/10">
                <p class="text-sm text-warning-700 dark:text-warning-300">{{ __('referrals::app.customer.pending') }}</p>
                <p class="mt-1 text-2xl font-bold text-warning-800 dark:text-warning-200">{{ number_format($this->pendingTotal, 2) }}</p>
            </div>
        </div>
    </div>
</x-filament-panels::page>
