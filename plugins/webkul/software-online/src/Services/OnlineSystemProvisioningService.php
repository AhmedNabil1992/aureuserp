<?php

namespace Webkul\SoftwareOnline\Services;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystem;

class OnlineSystemProvisioningService
{
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
            $url = rtrim($system->api_base_url, '/') . '/api/v1/ping';

            $response = $client->timeout(5)->get($url);

            return [
                'success' => $response->successful() || $response->status() === 404,
                'status'  => $response->status(),
                'message' => 'Connection reached server with status: ' . $response->status(),
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

            $payload = [
                'instance_number' => $instance->instance_number,
                'name'            => $instance->name,
                'subdomain'       => $instance->subdomain,
                'custom_domain'   => $instance->custom_domain,
                'plan_slug'       => $instance->plan?->slug,
                'admin_email'     => $instance->admin_email ?? $instance->partner?->email,
                'admin_username'  => $instance->admin_username,
                'billing_cycle'   => $instance->billing_cycle?->value,
                'expires_at'      => $instance->expires_at?->toIso8601String(),
                'custom_payload'  => $instance->plan?->custom_api_payload ?? [],
            ];

            $response = $client->post($url, $payload);

            if ($response->successful()) {
                $data = $response->json() ?? [];
                $remoteTenantId = $data['tenant_id'] ?? $data['id'] ?? $data['data']['id'] ?? null;
                $instanceUrl = $data['instance_url'] ?? $data['url'] ?? $data['data']['url'] ?? $instance->full_url;

                $instance->update([
                    'status'           => InstanceStatus::Active,
                    'remote_tenant_id' => $remoteTenantId ?? $instance->remote_tenant_id,
                    'instance_url'     => $instanceUrl,
                    'remote_data'      => $data,
                    'last_api_sync_at' => now(),
                    'last_api_error'   => null,
                ]);

                return true;
            }

            $error = "API Provision Failed ({$response->status()}): " . $response->body();
            Log::error($error);

            $instance->update([
                'status'         => InstanceStatus::Failed,
                'last_api_error' => $error,
            ]);

            return false;
        } catch (Exception $e) {
            Log::error('Exception during instance provisioning: ' . $e->getMessage());

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
    public function renewInstance(OnlineInstance $instance): bool
    {
        $system = $instance->system;
        if (! $system || empty($system->api_base_url) || empty($instance->remote_tenant_id)) {
            return true;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->renew_tenant_endpoint, $instance);

            $payload = [
                'tenant_id'     => $instance->remote_tenant_id,
                'billing_cycle' => $instance->billing_cycle?->value,
                'expires_at'    => $instance->expires_at?->toIso8601String(),
            ];

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
                'last_api_error' => "API Renew Failed ({$response->status()}): " . $response->body(),
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
            $instance->update(['status' => InstanceStatus::Suspended]);
            return true;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->suspend_tenant_endpoint, $instance);

            $response = $client->post($url, ['tenant_id' => $instance->remote_tenant_id]);

            $instance->update([
                'status'           => InstanceStatus::Suspended,
                'last_api_sync_at' => now(),
            ]);

            return $response->successful();
        } catch (Exception $e) {
            $instance->update(['status' => InstanceStatus::Suspended, 'last_api_error' => $e->getMessage()]);
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
            $instance->update(['status' => InstanceStatus::Active]);
            return true;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->activate_tenant_endpoint, $instance);

            $response = $client->post($url, ['tenant_id' => $instance->remote_tenant_id]);

            $instance->update([
                'status'           => InstanceStatus::Active,
                'last_api_sync_at' => now(),
            ]);

            return $response->successful();
        } catch (Exception $e) {
            $instance->update(['status' => InstanceStatus::Active, 'last_api_error' => $e->getMessage()]);
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
            return true;
        }

        try {
            $client = $this->buildHttpClient($system);
            $url = $this->formatUrl($system->api_base_url, $system->sync_status_endpoint, $instance);

            $response = $client->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $instance->update([
                    'remote_data'      => $data,
                    'last_api_sync_at' => now(),
                    'last_api_error'   => null,
                ]);
                return true;
            }

            return false;
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
        $url = rtrim($baseUrl, '/') . '/' . ltrim($endpoint, '/');

        $replacements = [
            '{tenant_id}' => $instance->remote_tenant_id ?? (string) $instance->id,
            '{subdomain}' => $instance->subdomain ?? '',
            '{instance_id}' => (string) $instance->id,
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $url);
    }
}
