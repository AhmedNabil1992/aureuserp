<?php

namespace Webkul\SoftwareOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OnlineSystem extends Model
{
    use SoftDeletes;

    protected $table = 'online_systems';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'icon',
        'logo',
        'base_url',
        'is_active',
        'sort_order',
        'api_driver',
        'api_base_url',
        'api_token',
        'api_secret',
        'api_headers',
        'create_tenant_endpoint',
        'renew_tenant_endpoint',
        'suspend_tenant_endpoint',
        'activate_tenant_endpoint',
        'delete_tenant_endpoint',
        'entitlements_endpoint',
        'sync_status_endpoint',
    ];

    protected $casts = [
        'is_active'    => 'boolean',
        'sort_order'   => 'integer',
        'api_headers'  => 'array',
        'api_token'    => 'encrypted',
        'api_secret'   => 'encrypted',
    ];

    public function plans(): HasMany
    {
        return $this->hasMany(OnlineSystemPlan::class, 'system_id')->orderBy('sort_order');
    }

    public function instances(): HasMany
    {
        return $this->hasMany(OnlineInstance::class, 'system_id');
    }

    public function webhookEvents(): HasMany
    {
        return $this->hasMany(OnlineTenantWebhookEvent::class, 'system_id');
    }

    public function tenantHost(?string $subdomain): ?string
    {
        $subdomain = strtolower(trim((string) $subdomain));
        $baseUrl = trim((string) ($this->base_url ?: $this->api_base_url));

        if ($subdomain === '' || $baseUrl === '') {
            return null;
        }

        if (! str_contains($baseUrl, '://')) {
            $baseUrl = 'https://'.$baseUrl;
        }

        if (str_contains($baseUrl, '{subdomain}')) {
            $host = parse_url(str_replace('{subdomain}', $subdomain, $baseUrl), PHP_URL_HOST);

            return is_string($host) && $host !== '' ? strtolower($host) : null;
        }

        $host = parse_url($baseUrl, PHP_URL_HOST);
        if (! is_string($host) || $host === '') {
            return null;
        }

        $host = preg_replace('/^www\./i', '', $host) ?: $host;

        return strtolower($subdomain.'.'.$host);
    }

    public function tenantUrl(?string $subdomain, string $path = ''): ?string
    {
        $host = $this->tenantHost($subdomain);
        if ($host === null) {
            return null;
        }

        $baseUrl = trim((string) ($this->base_url ?: $this->api_base_url));
        $scheme = str_contains($baseUrl, '://') ? parse_url($baseUrl, PHP_URL_SCHEME) : 'https';
        $scheme = is_string($scheme) && $scheme !== '' ? strtolower($scheme) : 'https';

        return $scheme.'://'.$host.($path === '' ? '' : '/'.ltrim($path, '/'));
    }

    public function tenantLoginUrl(?string $subdomain): ?string
    {
        return $this->tenantUrl($subdomain, 'admin/login');
    }
}
