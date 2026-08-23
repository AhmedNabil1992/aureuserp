<?php

namespace Webkul\SoftwareOnline\Filament\Customer\Pages;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Auth;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Models\OnlineSystem;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\SoftwareOnline\Services\OnlineBillingService;

class ExploreSystemsPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-sparkles';

    protected static string|\UnitEnum|null $navigationGroup = 'الأنظمة الأونلاين';

    protected static ?int $navigationSort = 1;

    protected string $view = 'software-online::filament.customer.pages.explore-systems';

    public string $billingPeriod = 'monthly'; // 'monthly' or 'annual'

    public ?int $selectedSystemId = null;

    public ?int $selectedPlanId = null;

    public string $websiteName = '';

    public string $subdomain = '';

    public string $adminUsername = 'admin';

    public string $adminEmail = '';

    public static function getNavigationGroup(): string|\UnitEnum|null
    {
        return \Webkul\Support\Enums\NavigationGroup::SoftwareOnline;
    }

    public static function getNavigationLabel(): string
    {
        return __('software-online::filament/customer/pages/explore.navigation.title');
    }

    public function getTitle(): string
    {
        return __('software-online::filament/customer/pages/explore.title');
    }

    public function mount(): void
    {
        $firstSystem = OnlineSystem::where('is_active', true)->orderBy('sort_order')->first();
        if ($firstSystem) {
            $this->selectedSystemId = $firstSystem->id;
        }

        $partner = Auth::guard('customer')->user();
        if ($partner) {
            $this->adminEmail = $partner->email ?? '';
        }
    }

    public function getSystemsProperty()
    {
        return OnlineSystem::where('is_active', true)
            ->with(['plans' => fn ($q) => $q->where('is_active', true)->orderBy('sort_order')])
            ->orderBy('sort_order')
            ->get();
    }

    public function getCustomerBalanceProperty(): float
    {
        $partner = Auth::guard('customer')->user();
        if (! $partner) {
            return 0.0;
        }

        return app(OnlineBillingService::class)->getAvailableBalance($partner);
    }

    public function selectPlan(int $planId): void
    {
        $this->selectedPlanId = $planId;
        $this->dispatch('open-modal', id: 'create-instance-modal');
    }

    public function createWebsite(): void
    {
        $this->validate([
            'selectedPlanId' => 'required|exists:online_system_plans,id',
            'websiteName'    => 'required|string|min:3|max:100',
            'subdomain'      => 'nullable|string|alpha_dash|max:50',
            'adminEmail'     => 'required|email',
        ]);

        $partner = Auth::guard('customer')->user();
        $plan = OnlineSystemPlan::findOrFail($this->selectedPlanId);
        $cycle = $this->billingPeriod === 'annual' ? BillingCycle::Annual : BillingCycle::Monthly;

        try {
            $instance = app(OnlineBillingService::class)->subscribeNewInstance(
                partner: $partner,
                plan: $plan,
                name: $this->websiteName,
                subdomain: $this->subdomain,
                cycle: $cycle,
                adminEmail: $this->adminEmail,
                adminUsername: $this->adminUsername
            );

            Notification::make()
                ->title(__('software-online::filament/customer/pages/explore.notifications.created_success'))
                ->body(__('software-online::filament/customer/pages/explore.notifications.created_desc', ['name' => $instance->name]))
                ->success()
                ->send();

            $this->dispatch('close-modal', id: 'create-instance-modal');
            $this->redirect(route('filament.customer.resources.my-online-websites.index'));
        } catch (\Exception $e) {
            Notification::make()
                ->title(__('software-online::filament/customer/pages/explore.notifications.failed'))
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
