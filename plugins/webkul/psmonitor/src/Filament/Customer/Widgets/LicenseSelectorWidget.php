<?php

namespace Webkul\Psmonitor\Filament\Customer\Widgets;

use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;
use Webkul\Software\Models\License;

class LicenseSelectorWidget extends Widget
{
    protected string $view = 'psmonitor::filament.customer.widgets.license-selector-widget';

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
            ->mapWithKeys(fn (License $license) => [
                $license->getKey() => $license->company_name ?: ($license->serial_number ?: __('psmonitor::filament/customer/widgets/license-selector.default_branch', ['id' => $license->getKey()])),
            ])
            ->toArray();
    }

    public function selectLicense(int $licenseId): void
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return;
        }

        $license = License::query()
            ->where('partner_id', $customer->id)
            ->remoteAccessible()
            ->where('id', $licenseId)
            ->first();

        if ($license) {
            app(CustomerLicenseResolver::class)->rememberSelectedLicense($license);
            $this->selected_license_id = $licenseId;
            $this->dispatch('refreshPage');
        }
    }

    #[On('refreshPage')]
    public function refreshPage(): void
    {
        $this->js('window.location.href = window.location.href;');
    }

    public static function canView(): bool
    {
        if (request()->routeIs('filament.customer.pages.dashboard')) {
            return false;
        }

        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return false;
        }

        return app(CustomerLicenseResolver::class)
            ->getAccessibleLicenses($customer)
            ->count() > 1;
    }
}