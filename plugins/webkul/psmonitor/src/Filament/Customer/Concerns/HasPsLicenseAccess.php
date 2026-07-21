<?php

namespace Webkul\Psmonitor\Filament\Customer\Concerns;

use Illuminate\Support\Facades\Auth;
use Webkul\Psmonitor\Services\CustomerLicenseResolver;

trait HasPsLicenseAccess
{
    /**
     * التحكم في الوصول بناءً على رخصة العميل
     */
    public static function canAccess(): bool
    {
        if (! Auth::guard('customer')->check()) {
            return false;
        }

        $customer = Auth::guard('customer')->user();

        $pluginProductId = [1]; 

        return app(CustomerLicenseResolver::class)
            ->hasAccessibleRemoteLicenseForProducts($customer, $pluginProductId);
    }
}