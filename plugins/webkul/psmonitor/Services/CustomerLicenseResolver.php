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
        return $this->resolveRemoteLicenseForProducts($customer, null, $licenseId);
    }

    public function resolveRemoteLicenseForProducts(Partner $customer, array $productIds = null, ?int $licenseId = null): ?License
    {
        $query = $customer->licenses()->remoteAccessible();

        $normalizedProductIds = collect($productIds ?? [])
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (! empty($normalizedProductIds)) {
            $query->whereIn('ProductID', $normalizedProductIds);
        }

        $targetLicenseId = $licenseId ?? $this->getSelectedLicenseId();

        // Clone base query to try preferred license first, then fall back to any available.
        $preferred = (clone $query)->orderByDesc('IsActive')->orderBy('Company_Name');

        if ($targetLicenseId !== null) {
            $license = (clone $preferred)->whereKey($targetLicenseId)->first();

            if ($license) {
                return $license;
            }

            // Selected license doesn't match this product filter — clear stale session value
            // and fall through to return the first available license for this product.
            if ($this->getSelectedLicenseId() === $targetLicenseId) {
                $this->forgetSelectedLicense();
            }
        }

        $license = $preferred->first();

        if (! $license) {
            throw new InvalidArgumentException('No license with an active Remote_Sub subscription is available for this customer.');
        }

        return $license;
    }

    public function hasAccessibleRemoteLicenseForProducts(Partner $customer, ?array $productIds = null): bool
    {
        try {
            $this->resolveRemoteLicenseForProducts($customer, $productIds);

            return true;
        } catch (\Throwable) {
            return false;
        }
    }

    public function rememberSelectedLicense(License $license): void
    {
        session([self::SESSION_KEY => (int) $license->getKey()]);
    }

    public function getSelectedLicenseId(): ?int
    {
        $licenseId = session(self::SESSION_KEY);

        return is_numeric($licenseId) ? (int) $licenseId : null;
    }

    public function forgetSelectedLicense(): void
    {
        session()->forget(self::SESSION_KEY);
    }
}