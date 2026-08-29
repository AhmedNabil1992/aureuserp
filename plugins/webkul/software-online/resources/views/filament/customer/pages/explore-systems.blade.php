<x-filament-panels::page>
    <!-- Balance & Intro Banner -->
    <!-- استخدمنا Inline Styles هنا عشان نمنع Tailwind إنه يمسح الخلفية -->
    <div class="p-6 rounded-2xl shadow-lg relative overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a, #3b82f6);">
        <div class="absolute inset-0 pointer-events-none" style="opacity: 0.15; background-image: radial-gradient(circle at 20% 30%, #ffffff 1px, transparent 1px); background-size: 24px 24px;"></div>
        <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div class="text-right">
                <h2 class="text-2xl font-bold tracking-tight" style="color: #ffffff;">{{ __('software-online::filament/customer/pages/explore.banner_title') }}</h2>
                <p class="text-sm mt-1 max-w-xl" style="color: #e2e8f0;">{{ __('software-online::filament/customer/pages/explore.banner_desc') }}</p>
            </div>
            <div class="px-5 py-3 rounded-xl flex items-center gap-3 shadow-sm" style="background-color: rgba(255, 255, 255, 0.15); border: 1px solid rgba(255, 255, 255, 0.2); backdrop-filter: blur(10px);">
                <div class="p-2 rounded-lg" style="background-color: rgba(16, 185, 129, 0.3); color: #a7f3d0;">
                    <x-filament::icon icon="heroicon-o-wallet" class="w-6 h-6" />
                </div>
                <div class="text-right">
                    <div class="text-xs" style="color: #e2e8f0;">{{ __('software-online::filament/customer/pages/explore.available_balance') }}</div>
                    <div class="text-xl font-extrabold tracking-wide" style="color: #ffffff;">{{ number_format($this->customerBalance, 2) }} EGP</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Systems Tabs & Period Toggle -->
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 mt-6">
        
        <!-- Systems List (استخدام أزرار Filament الأصلية) -->
        <div class="flex flex-wrap gap-2">
            @foreach($this->systems as $sys)
                <x-filament::button
                    color="{{ $selectedSystemId === $sys->id ? 'primary' : 'gray' }}"
                    wire:click="$set('selectedSystemId', {{ $sys->id }})"
                    size="md"
                >
                    {{ $sys->name }}
                </x-filament::button>
            @endforeach
        </div>

        <!-- Period Toggle (استخدام أزرار Filament الأصلية) -->
        <div class="flex items-center gap-2 p-1 bg-gray-100 dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700">
            <x-filament::button
                color="{{ $billingPeriod === 'monthly' ? 'primary' : 'gray' }}"
                wire:click="$set('billingPeriod', 'monthly')"
                size="sm"
            >
                {{ __('software-online::enums/billing-cycle.monthly') }}
            </x-filament::button>

            <x-filament::button
                color="{{ $billingPeriod === 'annual' ? 'primary' : 'gray' }}"
                wire:click="$set('billingPeriod', 'annual')"
                size="sm"
            >
                <span class="flex items-center gap-1.5">
                    <span>{{ __('software-online::enums/billing-cycle.annual') }}</span>
                    <x-filament::badge color="success" size="sm">خصم</x-filament::badge>
                </span>
            </x-filament::button>
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
                <div class="flex flex-col justify-between p-6 bg-white dark:bg-gray-900 rounded-2xl border border-gray-200 dark:border-gray-800 shadow-sm hover:shadow-lg transition-all duration-200 relative">
                    <div>
                        <div class="flex justify-between items-start mb-4">
                            <div>
                                <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ $plan->name }}</h3>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $plan->description ?? $activeSystem->name }}</p>
                            </div>
                        </div>

                        <div class="my-6">
                            <div class="flex items-baseline gap-1">
                                <!-- استخدام متغيرات Filament الأصلية للألوان -->
                                <span class="text-3xl font-extrabold" style="color: rgb(var(--primary-600));">{{ number_format($price, 2) }}</span>
                                <span class="text-xs text-gray-500 dark:text-gray-400 font-semibold">EGP / {{ $billingPeriod === 'annual' ? 'سنة' : 'شهر' }}</span>
                            </div>
                        </div>

                        <!-- Features list -->
                        <div class="space-y-2.5 my-6 text-sm text-gray-700 dark:text-gray-300">
                            @if($plan->max_users)
                                <div class="flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-success-500 flex-shrink-0" style="color: rgb(var(--success-500));" />
                                    <span>حتى {{ $plan->max_users }} مستخدمين</span>
                                </div>
                            @endif
                            @if($plan->max_branches)
                                <div class="flex items-center gap-2">
                                    <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-success-500 flex-shrink-0" style="color: rgb(var(--success-500));" />
                                    <span>حتى {{ $plan->max_branches }} فروع</span>
                                </div>
                            @endif
                            @if(is_array($plan->features))
                                @foreach($plan->features as $feature)
                                    <div class="flex items-center gap-2">
                                        <x-filament::icon icon="heroicon-o-check-circle" class="w-5 h-5 text-success-500 flex-shrink-0" style="color: rgb(var(--success-500));" />
                                        <span>{{ is_array($feature) ? ($feature['feature'] ?? '') : $feature }}</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>

                    <div class="pt-4 border-t border-gray-100 dark:border-gray-800 mt-4">
                        <!-- Subscribe Button (زر Filament الأصلي) -->
                        <x-filament::button
                            wire:click="selectPlan({{ $plan->id }})"
                            icon="heroicon-o-plus"
                            size="lg"
                            class="w-full justify-center"
                        >
                            {{ __('software-online::filament/customer/pages/explore.subscribe_btn') }}
                        </x-filament::button>
                    </div>
                </div>
            @empty
                <div class="col-span-full p-8 text-center bg-gray-50 dark:bg-gray-800/50 rounded-2xl border border-dashed border-gray-300 dark:border-gray-700 text-gray-500 dark:text-gray-400">
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

        @php
            $modalPlan = $activeSystem?->plans?->firstWhere('id', $selectedPlanId);
        @endphp

        <form wire:submit.prevent="createWebsite" class="space-y-4">
            <!-- Subscription Type Selector -->
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                    {{ __('software-online::filament/customer/pages/explore.modal.subscription_type') }}
                </label>
                
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2.5">
                    <!-- Trial Option (if not already used) -->
                    @if(! $this->hasUsedTrial)
                        <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all {{ $modalBillingCycle === 'trial' ? 'border-primary-500 bg-primary-50/40 dark:bg-primary-500/10 ring-2 ring-primary-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                            <input type="radio" wire:model.live="modalBillingCycle" value="trial" class="sr-only" />
                            <div class="flex justify-between items-center mb-1">
                                <span class="text-xs font-bold text-gray-900 dark:text-white">{{ __('software-online::filament/customer/pages/explore.modal.trial_option') }}</span>
                                <x-filament::badge color="warning" size="sm">مجاناً</x-filament::badge>
                            </div>
                            <span class="text-xs font-extrabold text-success-600 dark:text-success-400">0.00 EGP</span>
                            <span class="text-[10px] text-gray-500 mt-0.5">{{ $modalPlan?->trial_days > 0 ? $modalPlan->trial_days : 14 }} يوم (مرة واحدة)</span>
                        </label>
                    @endif

                    <!-- Monthly Option -->
                    <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all {{ $modalBillingCycle === 'monthly' ? 'border-primary-500 bg-primary-50/40 dark:bg-primary-500/10 ring-2 ring-primary-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="modalBillingCycle" value="monthly" class="sr-only" />
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ __('software-online::filament/customer/pages/explore.modal.monthly_option') }}</span>
                        </div>
                        <span class="text-xs font-extrabold text-primary-600 dark:text-primary-400">{{ number_format($modalPlan?->monthly_price ?? 0, 2) }} EGP</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">شهرياً من الرصيد</span>
                    </label>

                    <!-- Annual Option -->
                    <label class="relative flex flex-col p-3 rounded-xl border cursor-pointer transition-all {{ $modalBillingCycle === 'annual' ? 'border-primary-500 bg-primary-50/40 dark:bg-primary-500/10 ring-2 ring-primary-500' : 'border-gray-200 dark:border-gray-700 hover:border-gray-300' }}">
                        <input type="radio" wire:model.live="modalBillingCycle" value="annual" class="sr-only" />
                        <div class="flex justify-between items-center mb-1">
                            <span class="text-xs font-bold text-gray-900 dark:text-white">{{ __('software-online::filament/customer/pages/explore.modal.annual_option') }}</span>
                            <x-filament::badge color="success" size="sm">خصم</x-filament::badge>
                        </div>
                        <span class="text-xs font-extrabold text-primary-600 dark:text-primary-400">{{ number_format($modalPlan?->annual_price ?? 0, 2) }} EGP</span>
                        <span class="text-[10px] text-gray-500 mt-0.5">سنوياً من الرصيد</span>
                    </label>
                </div>

                @if($this->hasUsedTrial)
                    <p class="text-[11px] text-gray-500 dark:text-gray-400 mt-1.5 flex items-center gap-1">
                        <x-filament::icon icon="heroicon-o-information-circle" class="w-3.5 h-3.5 text-gray-400" />
                        <span>{{ __('software-online::filament/customer/pages/explore.modal.trial_used_notice') }}</span>
                    </p>
                @endif
            </div>

            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-1">
                    {{ __('software-online::filament/customer/pages/explore.modal.website_name') }}
                </label>
                <input
                    type="text"
                    wire:model.defer="websiteName"
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
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
                        class="flex-1 rounded-s-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm focus:border-primary-500 focus:ring-primary-500"
                        placeholder="elnoor-store"
                    />
                    <span class="inline-flex items-center px-3 rounded-e-xl border border-s-0 border-gray-300 dark:border-gray-700 bg-gray-50 dark:bg-gray-800 text-gray-500 dark:text-gray-400 text-xs">
                        @php
                            $parsedBase = parse_url($activeSystem->base_url ?? $activeSystem->api_base_url ?? '');
                            $baseHost = $parsedBase['host'] ?? null;
                            $domainSuffix = $baseHost ? '.' . preg_replace('/^(www\.)/', '', $baseHost) : config('software-online.subdomain_suffix', '.example.com');
                        @endphp
                        {{ $domainSuffix }}
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
                    class="w-full rounded-xl border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-900 dark:text-white placeholder-gray-400 dark:placeholder-gray-500 text-sm shadow-sm focus:border-primary-500 focus:ring-primary-500"
                    required
                />
            </div>

            @if($modalBillingCycle === 'trial')
                <div class="p-3 bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/20 rounded-xl text-xs text-emerald-700 dark:text-emerald-300 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-check-badge" class="w-5 h-5 flex-shrink-0" />
                    <span>{{ __('software-online::filament/customer/pages/explore.modal.trial_balance_notice') }}</span>
                </div>
            @else
                <div class="p-3 bg-amber-50 dark:bg-amber-500/10 border border-amber-200 dark:border-amber-500/20 rounded-xl text-xs text-amber-700 dark:text-amber-300 flex items-center gap-2">
                    <x-filament::icon icon="heroicon-o-information-circle" class="w-5 h-5 flex-shrink-0" />
                    <span>{{ __('software-online::filament/customer/pages/explore.modal.balance_notice') }}</span>
                </div>
            @endif

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