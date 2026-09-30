<?php

declare(strict_types=1);

namespace Webkul\Software\Tests\Feature;

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;
use Webkul\Software\Models\Firmware;

class FirmwareUpdateTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \TestBootstrapHelper::ensurePluginInstalled('software');
        Storage::fake('local');
    }

    public function test_headers_are_required(): void
    {
        $this->get('/api/iot/firmware')
            ->assertBadRequest()
            ->assertSeeText('Missing ESP8266 model or version header.');
    }

    public function test_unknown_model_returns_not_found(): void
    {
        $this->withHeaders($this->deviceHeaders('unknown', '1.0.0'))
            ->get('/api/iot/firmware')
            ->assertNotFound()
            ->assertSeeText('Unknown device model.');
    }

    public function test_current_firmware_returns_not_modified(): void
    {
        $this->createFirmware();

        $this->withHeaders($this->deviceHeaders('relay-active-low', '1.0.2'))
            ->get('/api/iot/firmware')
            ->assertNotModified();
    }

    public function test_older_firmware_downloads_the_binary_with_ota_headers(): void
    {
        $binary = "\xE9firmware-binary";
        $this->createFirmware($binary);

        $response = $this->withHeaders($this->deviceHeaders('relay-active-low', '1.0.1'))
            ->get('/api/iot/firmware');

        $response
            ->assertOk()
            ->assertHeader('content-type', 'application/octet-stream')
            ->assertHeader('x-MD5', md5($binary))
            ->assertHeader('x-ESP8266-model', 'relay-active-low')
            ->assertHeader('x-ESP8266-version', '1.0.2');

        self::assertSame($binary, $response->streamedContent());
    }

    private function createFirmware(string $binary = 'firmware'): Firmware
    {
        $path = 'iot-firmware/relay-active-low.bin';
        Storage::disk('local')->put($path, $binary);

        return Firmware::query()->create([
            'device_model'       => 'relay-active-low',
            'version'            => '1.0.2',
            'file_path'          => $path,
            'original_file_name' => 'latest-fw.bin',
            'is_active'          => true,
        ]);
    }

    private function deviceHeaders(string $model, string $version): array
    {
        return [
            'x-ESP8266-model'   => $model,
            'x-ESP8266-version' => $version,
        ];
    }
}
