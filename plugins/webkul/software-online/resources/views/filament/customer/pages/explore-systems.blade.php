<x-filament-panels::page>
    <!-- Balance & Intro Banner -->
    <div class="p-6 bg-gradient-to-r from-primary-600 via-primary-700 to-indigo-800 rounded-2xl shadow-xl text-white relative overflow-hidden">
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h2 class="text-2xl font-bold tracking-tight">{{ __('software-online::filament/customer/pages/explore.banner_title') }}</h2>
                <p class="text-primary-100 text-sm mt-1 max-w-xl">{{ __('software-online::filament/customer/pages/explore.banner_desc') }}</p>
            </div>
            <div class="bg-white/10 backdrop-blur-md px-5 py-3 rounded-xl border border-white/20 flex items-center gap-3">
                <div class="p-2 bg-emerald-500/20 rounded-lg text-emerald-300">
                    <x-filament::icon icon="heroicon-o-wallet" class="w-6 h-6" />
                </div>
                <div>
                    <div class="text-xs text-primary-200">{{ __('software-online::filament/customer/pages/explore.available_balance') }}</div>
                    <div class="text-xl font-extrabold text-white tracking-wide">{{ number_format($this->customerBalance, 2) }} EGP</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Systems Tabs & Period Toggle -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
        <!-- Systems List -->
        <div class="flex flex-wrap gap-2">
            @foreach($this->systems as $sys)
                <button
                    type="button"
                    wire:click="$set('selectedSystemId', {{ $sys->id }})"
                    class="px-4 py-2 text-sm font-semibold rounded-xl transition-all {{ $selectedSystemId === $sys->id ? 'bg-primary-600 text-white shadow-md' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}"
                >
                    {{ $sys->name }}
                </button>
            @endforeach
        </div>

        <!-- Period Toggle -->
        <div class="inline-flex p-1 bg-gray-200 dark:bg-gray-800 rounded-xl border border-gray-300 dark:border-gray-700">
            <button
                type="button"
                wire:click="$set('billingPeriod', 'monthly')"
                class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $billingPeriod === 'monthly' ? 'bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 shadow' : 'text-gray-600 dark:text-gray-400' }}"
            >
                {{ __('software-online::enums/billing-cycle.monthly') }}
            </button>
            <button
                type="button"
                wire:click="$set('billingPeriod', 'annual')"
                class="px-4 py-1.5 text-xs font-semibold rounded-lg transition-all {{ $billingPeriod === 'annual' ? 'bg-white dark:bg-gray-900 text-primary-600 dark:text-primary-400 shadow' : 'text-gray-600 dark:text-gray-400' }}"
            >
                {{ __('software-online::enums/billing-cycle.annual') }}
                <span class="ms-1 px-1.5 py-0.5 text-[10px] bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 rounded-full font-bold">خصم</span>
            </button>
        </div>
    </div>

    <!-- Active System Plans Grid -->
    @php
        $activeSystem = $this->systems->firstWhere('id', $selectedSystemId);
    @endphp

    @if($activeSystem)
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
            @forelse($activeSystem->plans as $plan)
                @php
                    $price = $billingPeriod === 'annual' ? $plan->annual_price : $plan->monthly_price;
                @endphp
                <div class="flex flex-col justify-between p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-xl transition-all duration-200 relative overflow-hidden group">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                                <p class="text-xs text-gray-500 mt-1">{{ $plan->description ?? $activeSystem->name }}</p>
                            </div>
                        </div>

                        <div class="my-6">
                            <div class="flex items-baseline gap-1">
                                <span class="text-3xl font-extrabold text-primary-600 dark:text-primary-400">{{ number_format($price, 2) }}</span>
                                <span class="text-xs text-gray-500 font-semibold">EGP / {{ $billingPeriod === 'annual' ? 'سنة' : 'شهر' }}</span>
                            </div>
                        </div>

                        <!-- Features list -->
                        <div class="space-y-2.5 my-6 text-sm text-gray-700 dark:text-gray-300">
                            @if($plan->max_users)
                                <div class="flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-4 h-4 text-emerald-500 flex-shrink-0" />
                                    <span>حتى {{ $plan->max_users }} مستخدمين</span>
                                </div>
                            @endif
                            @if($plan->max_branches)
                                <div class="flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-4 h-4 text-emerald-500 flex-shrink-0" />
                                    <span>حتى {{ $plan->max_branches }} فروع</span>
                                </div>
                            @endif
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <div class="flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-check-circle" class="w-4 h-4 text-emerald-500 flex-shrink-0" />
                                        <span>{{ is_array($feature) ? ($feature['feature'] ?? '') : $feature }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 mt-4">
                        <button
                            type="button"
                            wire:click="selectPlan({{ $plan->id }})"
                            class="w-full py-2.5 px-4 rounded-xl text-center font-bold text-sm bg-primary-600 hover:bg-primary-700 text-white shadow transition-all duration-150 flex items-center justify-center gap-2 group-hover:scale-[1.02]"
                        >
                            <x-filament::icon icon="heroicon-o-plus" class="w-4 h-4" />
                            <span>{{ __('software-online::filament/customer/pages/explore.subscribe_btn') }}</span>
                        </button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 text-gray-500">
                    {{ __('software-online::filament/customer/pages/explore.no_plans') }}
                </div>
            @endforelse
        </div>
    @endif

    <!-- Create Instance Modal -->
    <x-filament::modal id="create-instance-modal" width="lg">
        <x-slot name="heading">
            {{ __('software-online::filament/customer/pages/explore.modal.heading') }}
        </x-slot>

        <x-slot name="description">
            {{ __('software-online::filament/customer/pages/explore.modal.description') }}
        </x-slot>

        <form wire:submit.prevent="createWebsite" class="space-y-4">
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('software-online::filament/customer/pages/explore.modal.website_name') }}
                </label>
                <input
                    type="text"
                    wire:model.defer="websiteName"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    placeholder="متجر النور التجاري"
                    required
                />
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('software-online::filament/customer/pages/explore.modal.subdomain') }}
                </label>
                <div class="flex rounded-xl shadow-sm">
                    <input
                        type="text"
                        wire:model.defer="subdomain"
                        class="flex-1 rounded-s-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="elnoor-store"
                    />
                    <span class="inline-flex items-center px-3 rounded-e-xl border border-s-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 text-xs">
                        .poscloud.com
                    </span>
                </div>
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('software-online::filament/customer/pages/explore.modal.admin_email') }}
                </label>
                <input
                    type="email"
                    wire:model.defer="adminEmail"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 dark:bg-gray-900 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    required
                />
            </div>

            <div class="p-3 bg-amber-500/10 border border-amber-500/20 rounded-xl text-xs text-amber-700 dark:text-amber-300 flex items-center gap-2">
                <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 flex-shrink-0" />
                <span>{{ __('software-online::filament/customer/pages/explore.modal.balance_notice') }}</span>
            </div>

            <div class="flex justify-end gap-3 pt-4 border-t border-gray-200 dark:border-gray-700">
                <x-filament::button color="gray" type="button" x-on:click="$dispatch('close-modal', { id: 'create-instance-modal' })">
                    {{ __('software-online::filament/customer/pages/explore.modal.cancel') }}
                </x-filament::button>
                <x-filament::button type="submit" color="primary" icon="heroicon-o-check">
                    {{ __('software-online::filament/customer/pages/explore.modal.confirm_btn') }}
                </x-filament::button>
            </div>
        </form>
    </x-filament::modal>
</x-filament-panels::page>
