<?php

use Carbon\CarbonImmutable;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineSystem;
use Webkul\SoftwareOnline\Models\OnlineSystemPlan;

it('builds a fully qualified tenant host and login URL from the system domain', function (): void {
    $system = new OnlineSystem(['base_url' => 'https://eplaycloud.com']);

    expect($system->tenantHost('test4'))->toBe('test4.eplaycloud.com')
        ->and($system->tenantLoginUrl('test4'))->toBe('https://test4.eplaycloud.com/admin/login');
});

it('supports a subdomain placeholder in the system URL', function (): void {
    $system = new OnlineSystem(['base_url' => 'https://{subdomain}.eplaycloud.com']);

    expect($system->tenantHost('Client-One'))->toBe('client-one.eplaycloud.com')
        ->and($system->tenantLoginUrl('Client-One'))->toBe('https://client-one.eplaycloud.com/admin/login');
});

it('derives prices and expiration timestamps from the selected billing cycle', function (): void {
    $plan = new OnlineSystemPlan([
        'monthly_price' => 200,
        'annual_price'  => 2000,
        'trial_days'    => 7,
    ]);
    $startsAt = CarbonImmutable::parse('2026-09-28 10:00:00', 'UTC');

    expect($plan->priceFor(BillingCycle::Trial))->toBe(0.0)
        ->and($plan->priceFor(BillingCycle::Monthly))->toBe(200.0)
        ->and($plan->priceFor(BillingCycle::Annual))->toBe(2000.0)
        ->and($plan->expiresAtFor(BillingCycle::Trial, $startsAt)->toIso8601String())->toBe('2026-10-05T10:00:00+00:00')
        ->and($plan->expiresAtFor(BillingCycle::Monthly, $startsAt)->toIso8601String())->toBe('2026-10-28T10:00:00+00:00')
        ->and($plan->expiresAtFor(BillingCycle::Annual, $startsAt)->toIso8601String())->toBe('2027-09-28T10:00:00+00:00');
});

it('prefers the configured system domain over a stale remote instance URL', function (): void {
    $system = new OnlineSystem(['base_url' => 'https://eplaycloud.com']);
    $instance = new OnlineInstance([
        'subdomain'    => 'test4',
        'instance_url' => 'https://test4/admin/login',
    ]);
    $instance->setRelation('system', $system);

    expect($instance->full_url)->toBe('https://test4.eplaycloud.com/admin/login');
});
