<?php 

namespace Webkul\Psmonitor\Services;

use Illuminate\Support\Collection;
use InvalidArgumentException;
use Webkul\Partner\Models\Partner;
use Webkul\Software\Models\License;

class CustomerLicenseResolver
{
    public const SESSION_KEY = 'customer.selected_license_id';

    public function getAccessibleLicenses(Partner $customer): Collection
    {
        return License::query()
            ->where('partner_id', $customer->id)
            ->remoteAccessible()
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function resolveRemoteLicense(Partner $customer, ?int $licenseId = null): ?License
    {
        return $this->resolveRemoteLicenseForProducts($customer, null, $licenseId);
    }

    public function resolveRemoteLicenseForProducts(Partner $customer, ?array $productIds = null, ?int $licenseId = null): ?License
    {
        $query = License::query()
            ->where('partner_id', $customer->id)
            ->remoteAccessible();

        $normalizedProductIds = collect($productIds ?? [])
            ->filter(fn ($id) => is_numeric($id))
            ->map(fn ($id) => (int) $id)
            ->values()
            ->all();

        if (! empty($normalizedProductIds)) {
            $query->whereIn('program_id', $normalizedProductIds);
        }

        $targetLicenseId = $licenseId ?? $this->getSelectedLicenseId();

        $preferred = (clone $query)->orderByDesc('is_active')->orderBy('company_name');

        if ($targetLicenseId !== null) {
            $license = (clone $preferred)->whereKey($targetLicenseId)->first();

            if ($license) {
                return $license;
            }

            if ($this->getSelectedLicenseId() === $targetLicenseId) {
                $this->forgetSelectedLicense();
            }
        }

        $license = $preferred->first();

        if (! $license) {
            throw new InvalidArgumentException('No license with an active remote subscription is available for this customer.');
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
