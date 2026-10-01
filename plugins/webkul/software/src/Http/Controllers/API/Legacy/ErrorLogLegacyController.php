<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Throwable;
use Webkul\Software\Models\ErrorLog;
use Webkul\Software\Models\LicenseDevice;

class ErrorLogLegacyController extends Controller
{
    public function insert(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate($this->rules(5120, false));
            $errorLog = $this->createErrorLog($request, $validated);

            return response()->json([
                'ID'      => $errorLog->id,
                'Message' => 'Inserted successfully.',
            ]);
        } catch (Throwable $exception) {
            Log::error('Error inserting legacy software log.', ['exception' => $exception]);

            return response()->json([
                'error' => 'An error occurred while processing the request.',
            ], 500);
        }
    }

    public function insertV2(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate($this->rules(7168, true));
            $errorLog = $this->createErrorLog($request, $validated);

            return response()->json([
                'success' => true,
                'message' => 'Log inserted successfully',
                'data'    => [
                    'id'             => $errorLog->id,
                    'image_uploaded' => filled($errorLog->image_path),
                ],
            ], 201);
        } catch (ValidationException $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors'  => $exception->errors(),
            ], 422);
        } catch (Throwable $exception) {
            Log::error('Error inserting legacy software log.', ['exception' => $exception]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to insert log entry',
                'error'   => config('app.debug') ? $exception->getMessage() : 'Internal server error',
            ], 500);
        }
    }

    private function rules(int $imageSize, bool $requiresVersion): array
    {
        return [
            'EID'         => ['required', 'string', 'max:255'],
            'Device_Key'  => ['required', 'string', 'max:255'],
            'Date'        => ['required', 'date'],
            'Message'     => ['required', 'string'],
            'Trace'       => ['required', 'string'],
            'Form_Name'   => ['required', 'string', 'max:255'],
            'Image'       => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:'.$imageSize],
            'App_Version' => $requiresVersion ? ['required', 'string', 'max:50'] : ['nullable', 'string', 'max:50'],
        ];
    }

    private function createErrorLog(Request $request, array $validated): ErrorLog
    {
        $device = LicenseDevice::query()
            ->where('license_key', $validated['Device_Key'])
            ->orWhere('computer_id', $validated['Device_Key'])
            ->firstOrFail();

        $imagePath = $request->hasFile('Image')
            ? $request->file('Image')->store('logging', 'public')
            : null;

        return ErrorLog::query()->create([
            'device_id'   => $device->id,
            'eid'         => is_numeric($validated['EID']) ? (int) $validated['EID'] : null,
            'message'     => $validated['Message'],
            'trace'       => $validated['Trace'],
            'form_name'   => $validated['Form_Name'],
            'image_path'  => $imagePath,
            'app_version' => $validated['App_Version'] ?? null,
            'occurred_at' => $validated['Date'],
        ]);
    }
}
