<?php

declare(strict_types=1);

namespace Webkul\Software\Http\Controllers\IoT;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Software\Models\Firmware;

class FirmwareUpdateController extends Controller
{
    public function __invoke(Request $request): Response
    {
        $model = trim((string) $request->header('x-ESP8266-model'));
        $currentVersion = trim((string) $request->header('x-ESP8266-version'));

        if ($model === '' || $currentVersion === '') {
            return $this->plainText('Missing ESP8266 model or version header.', Response::HTTP_BAD_REQUEST);
        }

        $firmware = Firmware::query()
            ->where('device_model', $model)
            ->where('is_active', true)
            ->first();

        if (! $firmware) {
            return $this->plainText('Unknown device model.', Response::HTTP_NOT_FOUND);
        }

        if (version_compare($currentVersion, $firmware->version, '>=')) {
            return response('', Response::HTTP_NOT_MODIFIED, $this->noCacheHeaders());
        }

        $disk = Storage::disk('local');

        if (! $disk->exists($firmware->file_path)) {
            return $this->plainText('Firmware file not found.', Response::HTTP_NOT_FOUND);
        }

        return $disk->download(
            $firmware->file_path,
            $firmware->original_file_name,
            [
                ...$this->noCacheHeaders(),
                'Content-Type'      => 'application/octet-stream',
                'x-MD5'             => $firmware->md5,
                'x-ESP8266-model'   => $firmware->device_model,
                'x-ESP8266-version' => $firmware->version,
            ],
        );
    }

    private function plainText(string $message, int $status): Response
    {
        return response($message."\n", $status, [
            ...$this->noCacheHeaders(),
            'Content-Type' => 'text/plain; charset=UTF-8',
        ]);
    }

    private function noCacheHeaders(): array
    {
        return [
            'Cache-Control' => 'no-store, no-cache, must-revalidate, max-age=0',
            'Pragma'        => 'no-cache',
        ];
    }
}
