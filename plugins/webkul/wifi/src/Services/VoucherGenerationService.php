<?php

namespace Webkul\Wifi\Services;

use Carbon\CarbonInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use RuntimeException;
use Throwable;
use Webkul\Wifi\Enums\WifiPackageType;
use Webkul\Wifi\Models\WifiVoucherBatch;

class VoucherGenerationService
{
    /**
     * Generate a voucher batch on external Wi-Fi API from an existing local batch record.
     *
     * @return array{batch_code: string, download_url: ?string, status: int, response: array<string, mixed>}
     */
    public function generateFromBatch(
        WifiVoucherBatch $batch,
        ?int $profileId = null,
        ?int $daysValid = null,
        ?int $hoursValid = null,
        ?int $minutesValid = null,
        ?CarbonInterface $expireAt = null,
    ): array {
        $batch->loadMissing(['cloud', 'purchase.package']);

        if (! $batch->cloud_id) {
            throw new RuntimeException('Cloud is required to generate vouchers.');
        }

        if (! $batch->realm_id) {
            throw new RuntimeException('Realm is required to generate vouchers.');
        }

        if ((int) $batch->quantity < 1) {
            throw new RuntimeException('Batch quantity must be at least 1.');
        }

        $resolvedProfileId = $profileId ?? $batch->profile_id;

        if (! $resolvedProfileId) {
            throw new RuntimeException('Profile is required to generate vouchers.');
        }

        $endpoint = (string) config('services.wifi_voucher.endpoint');
        $token = (string) config('services.wifi_voucher.token');
        $language = (string) config('services.wifi_voucher.language', '4_4');

        if (blank($endpoint) || blank($token)) {
            throw new RuntimeException('Wi-Fi voucher API configuration is missing.');
        }

        $batchCode = $batch->batch_code ?: $this->generateBatchCode($batch->cloud?->name);

        $payload = [
            'single_field'      => 'true',
            'realm_id'          => $batch->realm_id,
            'profile_id'        => $resolvedProfileId,
            'quantity'          => (int) $batch->quantity,
            'batch'             => $batchCode,
            'activate_on_login' => 'on',
            'days_valid'        => max(0, $daysValid ?? (int) ($batch->days_valid ?? 0)),
            'hours_valid'       => max(0, $hoursValid ?? (int) ($batch->hours_valid ?? 0)),
            'minutes_valid'     => max(0, $minutesValid ?? (int) ($batch->minutes_valid ?? 0)),
            'extra_name'        => '',
            'extra_value'       => '',
            'token'             => $token,
            'sel_language'      => $language,
            'cloud_id'          => $batch->cloud_id,
        ];

        $isUnlimited = $this->isUnlimitedPackage($batch);

        if ($isUnlimited) {
            $payload['never_expire'] = 'on';
        } else {
            $resolvedExpireAt = $expireAt ?? now()->addMonth();
            $payload['expire'] = $resolvedExpireAt->format('m/d/Y');
        }

        $response = Http::asForm()->timeout(30)->post($endpoint, $payload);

        if (! $response->successful()) {
            throw new RuntimeException(sprintf(
                'Voucher API call failed (%d): %s',
                $response->status(),
                $response->body()
            ));
        }

        if ($batch->batch_code !== $batchCode) {
            $batch->batch_code = $batchCode;
            $batch->save();
        }

        return [
            'batch_code'   => $batchCode,
            'download_url' => $this->buildDownloadUrl($batchCode),
            'status'       => $response->status(),
            'response'     => $this->decodeResponse($response->body()),
        ];
    }

    private function isUnlimitedPackage(WifiVoucherBatch $batch): bool
    {
        if ($batch->never_expire) {
            return true;
        }

        $packageType = $batch->purchase?->package?->package_type;

        return $packageType instanceof WifiPackageType
            && $packageType === WifiPackageType::Unlimited;
    }

    private function generateBatchCode(?string $cloudName): string
    {
        $normalizedCloud = Str::of((string) $cloudName)
            ->replace(' ', '')
            ->upper()
            ->value();

        $normalizedCloud = $normalizedCloud !== '' ? $normalizedCloud : 'WIFI';

        return sprintf('%s_%s_%s', $normalizedCloud, now()->format('Ymd'), mt_rand(1, 100));
    }

    private function buildDownloadUrl(string $batchCode): ?string
    {
        if (app('router')->has('wifi.voucher-batches.download')) {
            return url('wifi/voucher-batches/'.rawurlencode($batchCode).'/download');
        }

        $baseUrl = (string) config('services.wifi_voucher.download_base_url');

        if (blank($baseUrl)) {
            return null;
        }

        return rtrim($baseUrl, '/').'?batch='.urlencode($batchCode);
    }

    /**
     * @return array<string, mixed>
     */
    private function decodeResponse(string $body): array
    {
        try {
            $decoded = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

            if (is_array($decoded)) {
                return $decoded;
            }
        } catch (Throwable) {
            // Keep fallback below.
        }

        return [
            'raw' => $body,
        ];
    }
}
