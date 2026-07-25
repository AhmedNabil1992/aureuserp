<?php

namespace Webkul\Wifi\Filament\Customer\Concerns;

use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Webkul\Partner\Models\Partner;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\WifiPartnerCloud;

/**
 * Provides helper methods for resolving the customer's
 * cloud IDs and realm names from the wifi_partner_clouds pivot.
 *
 * Use this trait in any Widget, Page, or Component that
 * needs to scope queries to the logged-in customer's clouds.
 */
trait HasCustomerCloudAccess
{
    /**
     * Get the authenticated customer (Partner).
     */
    protected function getCustomer(): ?Partner
    {
        $user = Auth::guard('customer')->user();

        return $user instanceof Partner ? $user : null;
    }

    /**
     * Get the cloud IDs assigned to the logged-in customer.
     */
    protected function getCustomerCloudIds(): array
    {
        $customer = $this->getCustomer();

        if (! $customer) {
            return [];
        }

        return WifiPartnerCloud::where('partner_id', $customer->id)
            ->pluck('cloud_id')
            ->toArray();
    }

    /**
     * Get the realm names associated with the customer's clouds.
     */
    protected function getCustomerRealmNames(): Collection
    {
        $cloudIds = $this->getCustomerCloudIds();

        if (empty($cloudIds)) {
            return collect();
        }

        return Realm::whereIn('cloud_id', $cloudIds)->pluck('name');
    }

    /**
     * Check whether the customer has any cloud subscriptions.
     */
    protected function hasCloudAccess(): bool
    {
        return ! empty($this->getCustomerCloudIds());
    }
}
