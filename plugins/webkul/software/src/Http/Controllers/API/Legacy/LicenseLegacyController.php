<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Http\Requests\API\Legacy\InsertKeysRequest;
use Webkul\Software\Http\Requests\API\Legacy\InsertLicenseRequest;
use Webkul\Software\Http\Requests\API\Legacy\LicenseInfoRequest;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseDevice;
use Webkul\Software\Services\LegacyLicenseKeyGenerator;
use Webkul\Support\Models\City;

class LicenseLegacyController extends Controller
{
    public function insertLicenses(InsertLicenseRequest $request): JsonResponse
    {
        $data = $request->validated();

        $programId = (int) $data['ProductID'];

        $stateId = $data['GoverID'] ?? null;
        $cityId = $data['CityID'] ?? null;

        if ($cityId) {
            $cityStateId = City::query()->whereKey($cityId)->value('state_id');

            if (! $cityStateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected city does not exist.',
                ], 422);
            }

            if ($stateId && (int) $stateId !== (int) $cityStateId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Selected city does not belong to selected governorate.',
                ], 422);
            }

            $stateId = (int) $cityStateId;
        }

        try {
            $licenseId = DB::transaction(function () use ($data, $programId, $stateId, $cityId): int {
                $license = License::query()->create([
                    'serial_number'  => $this->generateSerialNumber(),
                    'program_id'     => $programId,
                    'edition_id'     => null,
                    'partner_id'     => (int) $data['ClientID'],
                    'state_id'       => $stateId,
                    'city_id'        => $cityId,
                    'address'        => $data['Address'] ?? null,
                    'company_name'   => $data['CompanyName'],
                    'status'         => LicenseStatus::Pending->value,
                    'is_active'      => false,
                    'requested_at'   => now(),
                ]);

                return (int) $license->id;
            });

            return response()->json([
                'success' => true,
                'message' => 'Inserted',
                'data'    => [
                    'result' => $licenseId,
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: '.$exception->getMessage(),
            ], 500);
        }
    }

    public function insertKeys(InsertKeysRequest $request): JsonResponse
    {
        $data = $request->validated();

        $licenseId = (int) $data['License_ID'];

        try {
            $keyId = DB::transaction(function () use ($data, $licenseId): int {
                $device = LicenseDevice::query()->create([
                    'license_id'  => $licenseId,
                    'computer_id' => $data['Computer_ID'],
                    'bios_id'     => $data['Bios_ID'],
                    'disk_id'     => $data['Disk_ID'],
                    'base_id'     => $data['Base_ID'],
                    'video_id'    => $data['Video_ID'],
                    'mac_id'      => $data['Mac_ID'],
                ]);

                return (int) $device->id;
            });

            return response()->json([
                'success' => true,
                'message' => 'Inserted',
                'data'    => [
                    'result' => $keyId,
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred: '.$exception->getMessage(),
            ], 500);
        }
    }

    public function licenseInfo(LicenseInfoRequest $request, LegacyLicenseKeyGenerator $generator): JsonResponse
    {
        $data = $request->validated();

        try {
            $info = $generator->inspect($data['ProductKey'], $data['Computer_ID']);

            return response()->json([
                'ProductCode' => (string) $info['product_code'],
                'ProductKey'  => $data['ProductKey'],
                'LicenseType' => $info['license_type'],
                'Expiration'  => $info['license_type'] !== 'FULL' ? $info['expiration'] : 'Never',
                'Edition'     => $info['edition'],
                'IsMain'      => (string) $info['is_main'],
            ]);
        } catch (\RuntimeException) {
            return response()->json('Invalid license key.', 400);
        } catch (Throwable $exception) {
            return response()->json('Internal server error: '.$exception->getMessage(), 500);
        }
    }

    private function generateSerialNumber(): string
    {
        do {
            $serial = 'SL-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
        } while (License::query()->where('serial_number', $serial)->exists());

        return $serial;
    }
}
