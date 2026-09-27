<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Support\Facades\Validator;
use Webkul\Partner\Models\Partner;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystem;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;
use Webkul\SoftwareOnline\Models\OnlineTenantWebhookEvent;
use Webkul\SoftwareOnline\Rules\Subdomain;

beforeEach(function (): void {
    TestBootstrapHelper::ensurePluginInstalled('software-online');
});

function sendTenantWebhook(array $payload, OnlineSystem $system, string $secret, ?string $signature = null)
{
    $body = json_encode($payload, JSON_UNESCAPED_SLASHES | JSON_THROW_ON_ERROR);
    $timestamp = (string) now()->timestamp;
    $signature ??= 'sha256='.hash_hmac('sha256', $timestamp.'.'.$body, $secret);

    return test()->call(
        'POST',
        '/api/webhooks/ps-web/tenant-events',
        [],
        [],
        [],
        [
            'CONTENT_TYPE'             => 'application/json',
            'HTTP_ACCEPT'              => 'application/json',
            'HTTP_X_ONLINE_SYSTEM'     => $system->slug,
            'HTTP_X_WEBHOOK_TIMESTAMP' => $timestamp,
            'HTTP_X_WEBHOOK_SIGNATURE' => $signature,
        ],
        $body,
    );
}

it('accepts a signed tenant provisioned webhook and processes retries once', function (): void {
    $secret = 'test-webhook-secret';
    $system = OnlineSystem::create([
        'name'       => 'PS Web',
        'slug'       => 'ps-web',
        'is_active'  => true,
        'api_secret' => $secret,
    ]);
    $partner = Partner::factory()->create();
    $plan = OnlineSystemPlan::create([
        'system_id' => $system->id,
        'name'      => 'Starter',
        'slug'      => 'starter',
    ]);
    $instance = OnlineInstance::create([
        'partner_id'    => $partner->id,
        'system_id'     => $system->id,
        'plan_id'       => $plan->id,
        'name'          => 'Client Site',
        'subdomain'     => 'client-site',
        'billing_cycle' => BillingCycle::Monthly,
        'status'        => InstanceStatus::Provisioning,
        'starts_at'     => now(),
        'expires_at'    => now()->addMonth(),
    ]);

    $payload = [
        'event_id'   => 'evt-tenant-ready-1',
        'event_type' => 'tenant.provisioned',
        'occurred_at'=> now()->toIso8601String(),
        'data'       => [
            'tenant_id'               => '01REMOTEULID',
            'provisioning_request_id' => '01REQUESTULID',
            'external_subscription_id'=> 'online-instance:'.$instance->id,
            'domain'                  => 'client-site.example.com',
            'login_url'               => 'https://client-site.example.com/admin/login',
            'ended_at'                => now()->addMonth()->utc()->toIso8601String(),
        ],
    ];

    sendTenantWebhook($payload, $system, $secret)
        ->assertOk()
        ->assertJsonPath('status', 'processed');

    sendTenantWebhook($payload, $system, $secret)
        ->assertOk()
        ->assertJsonPath('status', 'duplicate');

    $instance->refresh();

    expect($instance->status)->toBe(InstanceStatus::Active)
        ->and($instance->remote_tenant_id)->toBe('01REMOTEULID')
        ->and($instance->provisioning_request_id)->toBe('01REQUESTULID')
        ->and($instance->instance_url)->toBe('https://client-site.example.com/admin/login')
        ->and(OnlineTenantWebhookEvent::where('event_id', 'evt-tenant-ready-1')->count())->toBe(1);
});

it('rejects a webhook with an invalid signature', function (): void {
    $system = OnlineSystem::create([
        'name'       => 'PS Web',
        'slug'       => 'ps-web',
        'is_active'  => true,
        'api_secret' => 'correct-secret',
    ]);

    sendTenantWebhook([
        'event_id'   => 'evt-invalid-signature',
        'event_type' => 'tenant.provisioned',
        'occurred_at'=> now()->toIso8601String(),
        'data'       => [],
    ], $system, 'correct-secret', 'sha256=invalid')->assertUnauthorized();
});

it('enforces lowercase customer-safe subdomains and reserved labels', function (): void {
    expect(Validator::make(['subdomain' => 'client-2026'], ['subdomain' => [new Subdomain]])->passes())->toBeTrue()
        ->and(Validator::make(['subdomain' => 'Client'], ['subdomain' => [new Subdomain]])->fails())->toBeTrue()
        ->and(Validator::make(['subdomain' => 'mail'], ['subdomain' => [new Subdomain]])->fails())->toBeTrue()
        ->and(Validator::make(['subdomain' => 'mqtt'], ['subdomain' => [new Subdomain]])->fails())->toBeTrue()
        ->and(Validator::make(['subdomain' => '-client'], ['subdomain' => [new Subdomain]])->fails())->toBeTrue();
});
