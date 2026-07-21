<?php

namespace Webkul\Wifi\Filament\Customer\Concerns;

use Illuminate\Support\Facades\Auth;
use Webkul\Wifi\Models\WifiPartnerCloud;

trait HasWifiAccess
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

        return WifiPartnerCloud::where('partner_id', $customer->id)->exists();
    }
}


