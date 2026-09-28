<?php

namespace Webkul\SoftwareOnline\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystem;

class OnlineSystemProvisioningService
{
    /**
     * Verify availability against the remote system before any local creation
     * or billing work starts. The provisioning endpoint still performs the
     * final atomic reservation to protect against concurrent requests.
     */
    public function assertDomainAvailable(OnlineSystem $system, string $subdomain): string
    {
        $domain = $system->tenantHost($subdomain);
        $endpoint = filled($system->check_domain_endpoint)
            ? (string) $system->check_domain_endpoint
            : '/api/tenants/check-domain';

        if (blank($domain) || blank($system->api_base_url)) {
            Log::warning('Remote tenant domain check is not configured.', [
                'online_system_id'   => $system->getKey(),
                'online_system_slug' => $system->slug,
                'has_api_base_url'   => filled($system->api_base_url),
                'domain'             => $domain,
            ]);

            throw ValidationException::withMessages([
                'subdomain' => __('software-online::validation.domain_check_failed'),
            ]);
        }

        try {
            $url = rtrim((string) $system->api_base_url, '/').'/'.ltrim($endpoint, '/');
            $response = $this->buildHttpClient($system)->get($url, ['domain' => $domain]);
        } catch (Exception $exception) {
            Log::warning('Remote tenant domain check request failed.', [
                'online_system_id'   => $system->getKey(),
                'online_system_slug' => $system->slug,
                'domain'             => $domain,
                'error'              => $exception->getMessage(),
            ]);

            throw ValidationException::withMessages([
                'subdomain' => __('software-online::validation.domain_check_failed'),
            ]);
        }

        if (! $response->successful() || ! is_bool($response->json('available'))) {
            Log::warning('Remote tenant domain check returned an invalid response.', [
                'online_system_id'   => $system->getKey(),
                'online_system_slug' => $system->slug,
                'domain'             => $domain,
                'status'             => $response->status(),
            ]);

            throw ValidationException::withMessages([
                'subdomain' => __('software-online::validation.domain_check_failed'),
            ]);
        }

        if (! $response->json('available')) {
            throw ValidationException::withMessages([
                'subdomain' => __('software-online::validation.domain_taken'),
            ]);
        }

        return $domain;
    }

    /**
     * Test connection to system API
     */
    public function testConnection(OnlineSystem $system): array
    {
        if (empty($system->api_base_url)) {
            return [
                'success' => false,
                'message' => 'API Base URL is not configured.',
            ];
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = rtrim($system->api_base_url, '/').'/api/v1/ping';

            $response = $client->timeout(5)->get($url);

            return [
                'success' => $response->successful() || $response->status() === 404,
                'status'  => $response->status(),
                'message' => 'Connection reached server with status: '.$response->status(),
            ];
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }

    /**
     * Provision remote tenant on the target system
     */
    public function provisionInstance(OnlineInstance $instance): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url)) {
            $instance->update([
                'status'          => InstanceStatus::Active,
                'last_api_error'  => null,
                'last_api_sync_at'=> now(),
            ]);

            return true;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->create_tenant_endpoint, $instance);

            $customPayload = $instance->plan?->custom_api_payload ?? [];
            $payload = array_merge($customPayload, [
                'domain'                   => $this->resolveDomain($instance),
                'name'                     => $instance->partner?->name ?? $instance->name,
                'email'                    => $instance->partner?->email,
                'started_at'               => $instance->starts_at?->utc()->toIso8601String(),
                'ended_at'                 => $instance->expires_at?->utc()->toIso8601String(),
                'user_id'                  => $instance->partner_id,
                'external_subscription_id' => 'online-instance:'.$instance->id,
                'auto_renew'               => $instance->auto_renew,
                'branches_limit'           => $instance->plan?->max_branches ?? 1,
                'has_ai_subscription'      => (bool) ($customPayload['has_ai_subscription'] ?? true),
                'ai_requests_limit'        => (int) ($customPayload['ai_requests_limit'] ?? 10),
            ]);

            $response = $client
                ->withHeader('Idempotency-Key', 'tenant-provision:'.$system->id.':'.$instance->id)
                ->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json() ?? [];
                $remoteTenantId = $data['tenant_id'] ?? $data['id'] ?? $data['data']['id'] ?? null;
                $provisioningRequestId = $data['provisioning_request_id'] ?? $data['request_id'] ?? null;
                $instanceUrl = $data['instance_url'] ?? $data['url'] ?? $data['data']['url'] ?? $instance->full_url;

                $instance->update([
                    'status'           => $response->status() === 202 || ! $remoteTenantId
                        ? InstanceStatus::Provisioning
                        : InstanceStatus::Active,
                    'remote_tenant_id'        => $remoteTenantId ?? $instance->remote_tenant_id,
                    'provisioning_request_id' => $provisioningRequestId ?? $instance->provisioning_request_id,
                    'instance_url'            => $instanceUrl,
                    'remote_data'             => $data,
                    'last_api_sync_at'        => now(),
                    'last_api_error'          => null,
                ]);

                return true;
            }

            $error = "API Provision Failed ({$response->status()}): ".$response->body();
            Log::error($error);

            $instance->update([
                'status'         => InstanceStatus::Failed,
                'last_api_error' => $error,
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Exception during instance provisioning: '.$e->getMessage());

            $instance->update([
                'status'         => InstanceStatus::Failed,
                'last_api_error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Renew remote tenant subscription
     */
    public function renewInstance(OnlineInstance $instance, ?string $idempotencyKey = null): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url)) {
            return true;
        }

        if (empty($instance->remote_tenant_id)) {
            $instance->update([
                'last_api_error' => 'Remote tenant ID is missing; renewal remains pending.',
            ]);

            return false;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->renew_tenant_endpoint, $instance);

            $payload = [
                'ended_at' => $instance->expires_at?->utc()->toIso8601String(),
            ];

            if ($idempotencyKey) {
                $client = $client->withHeader('Idempotency-Key', $idempotencyKey);
            }

            $response = $client->post($url, $payload);

            if ($response->successful()) {
                $instance->update([
                    'status'           => InstanceStatus::Active,
                    'last_api_sync_at' => now(),
                    'last_api_error'   => null,
                ]);

                return true;
            }

            $instance->update([
                'last_api_error' => "API Renew Failed ({$response->status()}): ".$response->body(),
            ]);

            return false;
        } catch (Exception $e) {
            $instance->update(['last_api_error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Suspend remote tenant
     */
    public function suspendInstance(OnlineInstance $instance): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url) || empty($instance->remote_tenant_id)) {
            $instance->update(['last_api_error' => 'Remote API URL and tenant ID are required before suspension.']);

            return false;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->suspend_tenant_endpoint, $instance);

            $response = $client->post($url, ['tenant_id' => $instance->remote_tenant_id]);

            if (! $response->successful()) {
                $instance->update(['last_api_error' => $this->responseError($response->status(), $response->body())]);

                return false;
            }

            $instance->update([
                'status'           => InstanceStatus::Suspended,
                'last_api_sync_at' => now(),
                'last_api_error'   => null,
            ]);

            return true;
        } catch (Exception $e) {
            $instance->update(['last_api_error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Activate remote tenant
     */
    public function activateInstance(OnlineInstance $instance): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url) || empty($instance->remote_tenant_id)) {
            $instance->update(['last_api_error' => 'Remote API URL and tenant ID are required before activation.']);

            return false;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->activate_tenant_endpoint, $instance);

            $response = $client->post($url, ['tenant_id' => $instance->remote_tenant_id]);

            if (! $response->successful()) {
                $instance->update(['last_api_error' => $this->responseError($response->status(), $response->body())]);

                return false;
            }

            $instance->update([
                'status'           => InstanceStatus::Active,
                'last_api_sync_at' => now(),
                'last_api_error'   => null,
            ]);

            return true;
        } catch (Exception $e) {
            $instance->update(['last_api_error' => $e->getMessage()]);

            return false;
        }
    }

    /**
     * Sync live remote status
     */
    public function syncStatus(OnlineInstance $instance): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url) || empty($instance->remote_tenant_id)) {
            $instance->update(['last_api_error' => 'Remote API URL and tenant ID are required before status synchronization.']);

            return false;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->sync_status_endpoint, $instance);

            $response = $client->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $data = is_array($data) ? $data : [];
                $attributes = [
                    'remote_data'      => $data,
                    'last_api_sync_at' => now(),
                    'last_api_error'   => null,
                ];

                $attributes['status'] = match ($data['status'] ?? null) {
                    'ready'     => InstanceStatus::Active,
                    'suspended' => InstanceStatus::Suspended,
                    'deleting'  => InstanceStatus::Deleting,
                    'deleted'   => InstanceStatus::Deleted,
                    'failed'    => InstanceStatus::Failed,
                    default     => $instance->status,
                };

                if (filled($data['ended_at'] ?? null)) {
                    $attributes['expires_at'] = $data['ended_at'];
                }

                $instance->update($attributes);

                return true;
            }

            $instance->update(['last_api_error' => $this->responseError($response->status(), $response->body())]);

            return false;
        } catch (Exception $e) {
            $instance->update(['last_api_error' => $e->getMessage()]);

            return false;
        }
    }

    public function updateEntitlements(OnlineInstance $instance): bool
    {
        $instance->loadMissing('plan', 'system');
        $system = $instance->system;
        if (! $system || empty($system->api_base_url) || empty($system->entitlements_endpoint) || empty($instance->remote_tenant_id)) {
            $instance->update(['last_api_error' => 'Remote API URL, entitlements endpoint, and tenant ID are required before entitlement synchronization.']);

            return false;
        }

        $customPayload = $instance->plan?->custom_api_payload ?? [];
        $payload = [
            'branches_limit'      => max(1, (int) ($instance->plan?->max_branches ?? 1)),
            'has_ai_subscription' => (bool) ($customPayload['has_ai_subscription'] ?? true),
            'ai_requests_limit'   => max(0, (int) ($customPayload['ai_requests_limit'] ?? 10)),
        ];

        try {
            $url = $this->formatUrl($system->api_base_url, $system->entitlements_endpoint, $instance);
            $idempotencyKey = 'tenant-entitlements:'.$instance->id.':'.hash('sha256', json_encode($payload, JSON_THROW_ON_ERROR));
            $response = $this->buildHttpClient($system)
                ->withHeader('Idempotency-Key', $idempotencyKey)
                ->patch($url, $payload);

            if (! $response->successful()) {
                $instance->update(['last_api_error' => $this->responseError($response->status(), $response->body())]);

                return false;
            }

            $responseData = $response->json();
            $instance->update([
                'remote_data'      => array_replace_recursive(
                    $instance->remote_data ?? [],
                    is_array($responseData) ? $responseData : [],
                ),
                'last_api_sync_at' => now(),
                'last_api_error'   => null,
            ]);

            return true;
        } catch (Exception $e) {
            $instance->update(['last_api_error' => $e->getMessage()]);

            return false;
        }
    }

    public function deleteInstance(OnlineInstance $instance): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url) || empty($system->delete_tenant_endpoint) || empty($instance->remote_tenant_id)) {
            $instance->update(['last_api_error' => 'Remote API URL, delete endpoint, and tenant ID are required before deletion.']);

            return false;
        }

        try {
            $url = $this->formatUrl($system->api_base_url, $system->delete_tenant_endpoint, $instance);
            $response = $this->buildHttpClient($system)
                ->withHeader('Idempotency-Key', 'tenant-delete:'.$instance->id)
                ->delete($url);

            if (! $response->successful()) {
                $instance->update(['last_api_error' => $this->responseError($response->status(), $response->body())]);

                return false;
            }

            $instance->update([
                'status'           => InstanceStatus::Deleting,
                'last_api_sync_at' => now(),
                'last_api_error'   => null,
            ]);

            return true;
        } catch (Exception $e) {
            $instance->update(['last_api_error' => $e->getMessage()]);

            return false;
        }
    }

    protected function buildHttpClient(OnlineSystem $system)
    {
        $client = Http::timeout(15)->acceptJson();

        if (! empty($system->api_token)) {
            $client->withToken($system->api_token);
        }

        if (! empty($system->api_headers) && is_array($system->api_headers)) {
            $client->withHeaders($system->api_headers);
        }

        return $client;
    }

    protected function formatUrl(string $baseUrl, string $endpoint, OnlineInstance $instance): string
    {
        $url = rtrim($baseUrl, '/').'/'.ltrim($endpoint, '/');

        $replacements = [
            '{tenant_id}'   => $instance->remote_tenant_id ?? (string) $instance->id,
            '{subdomain}'   => $instance->subdomain ?? '',
            '{instance_id}' => (string) $instance->id,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $url);
    }

    private function responseError(int $status, string $body): string
    {
        return "Remote API request failed ({$status}): ".mb_strimwidth($body, 0, 2000, '...');
    }

    private function resolveDomain(OnlineInstance $instance): string
    {
        if (filled($instance->custom_domain)) {
            $customDomain = str_contains((string) $instance->custom_domain, '://')
                ? (string) $instance->custom_domain
                : 'https://'.$instance->custom_domain;
            $host = parse_url($customDomain, PHP_URL_HOST);

            if (is_string($host) && $host !== '') {
                return strtolower($host);
            }
        }

        return $instance->system?->tenantHost($instance->subdomain)
            ?? strtolower((string) $instance->subdomain);
    }
}
