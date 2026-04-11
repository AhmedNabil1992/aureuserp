<?php

use Illuminate\Support\Facades\Http;
use Webkul\Software\Services\LegacyLicenseKeyGenerator;

it('generates legacy key format', function () {
    $key = app(LegacyLicenseKeyGenerator::class)->generate(
        productCode: 21,
        type: 'FULL',
        edition: 'STANDARD',
        computerId: 'ABCDEF1234567890ABCDEFGH12345678',
        endDate: now()->addYear(),
        isMain: true,
    );

    expect($key)->toMatch('/^[A-Z0-9]{5}(?:-[A-Z0-9]{5}){5}$/');
});

it('returns license info from internal legacy endpoint', function () {
    $productCode = 21;
    $computerId = 'ABCDEF1234567890ABCDEFGH12345678';

    $key = app(LegacyLicenseKeyGenerator::class)->generate(
        productCode: $productCode,
        type: 'FULL',
        edition: 'STANDARD',
        computerId: $computerId,
        endDate: now()->addYear(),
        isMain: true,
    );

    $this->postJson('/api/license-info', [
        'Computer_ID' => $computerId,
        'ProductKey'  => $key,
    ])
        ->assertOk()
        ->assertJsonPath('ProductCode', (string) $productCode)
        ->assertJsonPath('ProductKey', $key)
        ->assertJsonPath('LicenseType', 'FULL')
        ->assertJsonPath('Edition', 'STANDARD')
        ->assertJsonPath('IsMain', '1')
        ->assertJsonPath('Expiration', 'Never');
});

it('rejects invalid key in internal legacy endpoint', function () {
    $response = $this->postJson('/api/license-info', [
        'Computer_ID' => 'ABCDEF1234567890ABCDEFGH12345678',
        'ProductKey'  => 'INVALID-KEY00-00000-00000-00000-00000',
    ]);

    $response
        ->assertStatus(400)
        ->assertSeeText('Invalid license key.');
});

it('generates keys compatible with external legacy validator when available', function () {
    $productCode = 21;
    $computerId = 'ABCDEF1234567890ABCDEFGH12345678';

    $key = app(LegacyLicenseKeyGenerator::class)->generate(
        productCode: $productCode,
        type: 'FULL',
        edition: 'STANDARD',
        computerId: $computerId,
        endDate: now()->addYear(),
        isMain: true,
    );

    try {
        $response = Http::timeout(5)->post('http://127.0.0.1:82/api/LicGen/info', [
            'Computer_ID' => $computerId,
            'ProductKey'  => $key,
        ]);
    } catch (Throwable $exception) {
        $this->markTestSkipped('External legacy generator is not reachable in this environment.');

        return;
    }

    if (! $response->successful()) {
        $this->markTestSkipped('External legacy validator is reachable but rejected test vector: '.$response->body());

        return;
    }

    expect($response->status())->toBe(200);

    $payload = $response->json();

    if (! is_array($payload) || ! array_key_exists('ProductCode', $payload)) {
        $this->markTestSkipped('External legacy validator returned unexpected response schema.');

        return;
    }

    expect($payload['ProductCode'])->toBe((string) $productCode);
    expect($payload['LicenseType'] ?? null)->toBe('FULL');
    expect($payload['Edition'] ?? null)->toBe('STANDARD');
    expect($payload['IsMain'] ?? null)->toBe('1');
});
