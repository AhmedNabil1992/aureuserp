<?php 

namespace Webkul\Psmonitor\Services;

use Webkul\Partner\Models\Partner;
use Webkul\Software\Models\License;
use Illuminate\Support\Collection;
use InvalidArgumentException;

class CustomerLicenseResolver
{
    public const SESSION_KEY = 'customer.selected_license_id';

    public function getAccessibleLicenses(Partner $customer): Collection
    {
        return License::query()
            ->where('partner_id', $customer->id)
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function resolveRemoteLicense(Partner $customer, ?int $licenseId = null): ?License
    {
        
    }

    public function resolveRemoteLicenseForProducts
}