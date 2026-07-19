<?php

namespace Webkul\Wifi\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Throwable;
use Webkul\Wifi\Models\PermanentUser;
use Webkul\Wifi\Models\Profile;
use Webkul\Wifi\Models\Realm;

class PermanentUserService
{
    /**
     * @param  array{username: string, password: string, cloud_id: int|string, realm: int|string, profile_id: int|string}  $data
     * @return array<string, mixed>
     */
    public function create(array $data): array
    {
        $endpoint = $this->resolveEndpoint('add');
        $token = (string) config('services.wifi_voucher.token');
        $language = (string) config('services.wifi_voucher.language', '4_4');

        if (blank($endpoint) || blank($token)) {
            throw new RuntimeException(__('wifi::filament/resources/permanent_user.messages.api_missing'));
        }

        $realmId = (int) $data['realm'];
        $profileId = (int) $data['profile_id'];

        $realm = Realm::query()->find($realmId)?->name;
        $profile = Profile::query()->find($profileId)?->name;

        if (blank($realm) || blank($profile)) {
            throw new RuntimeException(__('wifi::filament/resources/permanent_user.messages.invalid_data'));
        }

        $payload = [
            'username'     => (string) $data['username'],
            'password'     => (string) $data['password'],
            'realm'        => (string) $realm,
            'realm_id'     => $realmId,
            'profile'      => (string) $profile,
            'profile_id'   => $profileId,
            'cloud_id'     => (int) $data['cloud_id'],
            'token'        => $token,
            'active'       => 'active',
            'sel_language' => $language,
        ];

        $response = Http::asJson()
            ->timeout(30)
            ->post($endpoint, $payload);

        return $this->validateResponse($response, 'Failed to create permanent user.');
    }

    /**
     * @return array<string, mixed>
     */
    public function delete(PermanentUser $record): array
    {
        $endpoint = $this->resolveEndpoint('delete');
        $token = (string) config('services.wifi_voucher.token');

        if (blank($endpoint) || blank($token)) {
            throw new RuntimeException(__('wifi::filament/resources/permanent_user.messages.api_missing'));
        }

        $response = Http::asJson()
            ->timeout(30)
            ->post($endpoint.'?token='.urlencode($token).'&cloud_id='.(int) $record->cloud_id, [
                ['id' => (int) $record->id],
            ]);

        return $this->validateResponse($response, 'Failed to delete permanent user.');
    }

    /**
     * @return array<string, mixed>
     */
    public function addtopup(array $data): array
    {
        $endpoint = $this->resolveEndpoint('topup');
        $token = (string) config('services.wifi_voucher.token');

        $payload = [
            'permanent_user_id' => (int) $data['permanent_user_id'],
            'type' => strtolower((string) $data['type']),
            'value' => (int) $data['value'],
            'data_unit' => strtolower((string) $data['data_unit']),
            'comment' => (string) ($data['comment'] ?? ''),
            'token' => $token,
            'cloud_id' => (int) $data['cloud_id'],
        ];

        $response = Http::asForm()
            ->timeout(30)
            ->post($endpoint, $payload);

        return $this->validateResponse($response, 'Failed to add top-up.');

    }

    private function resolveEndpoint(string $action): string
    {
        $configuredEndpoint = (string) config("services.wifi_voucher.permanent_user_{$action}_endpoint");

        if (filled($configuredEndpoint)) {
            return $configuredEndpoint;
        }

        $voucherEndpoint = (string) config('services.wifi_voucher.endpoint');

        if (blank($voucherEndpoint)) {
            return '';
        }

        return match ($action) {
            'add'    => str_replace('/vouchers/add.json', '/permanent-users/add.json', $voucherEndpoint),
            'delete' => str_replace('/vouchers/add.json', '/permanent-users/delete.json', $voucherEndpoint),
            'topup'  => str_replace('/vouchers/add.json', '/top-ups/add.json', $voucherEndpoint),
            default  => $voucherEndpoint,
        };
    }

    /**
     * @return array<string, mixed>
     */
    private function validateResponse(Response $response, string $fallbackMessage): array
    {
        if (! $response->successful()) {
            throw new RuntimeException(sprintf('%s (%d): %s', $fallbackMessage, $response->status(), $response->body()));
        }

        $decodedResponse = $this->decodeResponse($response->body());

        if (isset($decodedResponse['success']) && $decodedResponse['success'] !== true) {
            $message = (string) ($decodedResponse['message'] ?? $fallbackMessage);
            throw new RuntimeException($message);
        }

        return $decodedResponse;
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
