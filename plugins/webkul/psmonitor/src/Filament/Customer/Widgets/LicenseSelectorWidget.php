<?php

namespace Webkul\PSMonitor\Filament\Customer\Widgets;

use Webkul\PSMonitor\Services\CustomerLicenseResolver;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class LicenseSelectorWidget extends Widget
{
    protected static string $view = 'psmonitor::filament.customer.widgets.license-selector-widget';

    protected static bool $isLazy = false;

    /** @var int|null */
    public ?int $selected_license_id = null;

    public function mount(): void
    {
        $this->selected_license_id = app(CustomerLicenseResolver::class)->getSelectedLicenseId();
    }
    
    public function getLicenseOptions(): array
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return [];
        }

        return app(CustomerLicenseResolver::class)
            ->getAccessibleLicenses($customer)
            ->mapWithKeys(fn ($license) => [$license->getKey() => $license->Company_Name])
            ->toArray();
    }

    public function selectLicense(int $licenseId): void
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return;
        }

        $license = $customer->licenses()
            ->remoteAccessible()
            ->where('ID', $licenseId)
            ->first();

        if ($license) {
            app(CustomerLicenseResolver::class)->rememberSelectedLicense($license);
            $this->selected_license_id = $licenseId;
            // Use JavaScript to reload page properly (avoids Livewire GET redirect issue)
            $this->dispatch('refreshPage');
        }
    }

    #[On('refreshPage')]
    public function refreshPage(): void
    {
        // This will trigger a real page reload via browser
        $this->js('window.location.href = window.location.href;');
    }

    /**
     * Only render when multiple licenses available.
     * Widget is only explicitly added to specific Pages via getHeaderWidgets().
     */
    public static function canView(): bool
    {
        if (request()->routeIs('filament.customer.pages.dashboard')) {
            return false;
        }

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return false;
        }

        // Only show if there's more than one accessible license
        return app(CustomerLicenseResolver::class)
            ->getAccessibleLicenses($customer)
            ->count() > 1;
    }
}