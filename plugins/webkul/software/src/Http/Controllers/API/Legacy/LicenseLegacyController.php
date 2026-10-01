<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Http\Requests\API\Legacy\InsertKeysRequest;
use Webkul\Software\Http\Requests\API\Legacy\InsertLicenseRequest;
use Webkul\Software\Http\Requests\API\Legacy\LicenseInfoRequest;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseDevice;
use Webkul\Software\Models\LicenseShiftEmail;
use Webkul\Software\Models\LicenseSubscription;
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
                'message' => app()->isProduction() ? 'An error occurred. Please try again later.' : 'An error occurred: '.$exception->getMessage(),
            ], 500);
        }
    }

    public function insertKeys(InsertKeysRequest $request): JsonResponse
    {
        $data = $request->validated();

        $licenseId = (int) $data['License_ID'];

        try {
            $result = DB::transaction(function () use ($data, $licenseId): array {
                License::query()->whereKey($licenseId)->lockForUpdate()->firstOrFail();

                $device = LicenseDevice::query()
                    ->where('license_id', $licenseId)
                    ->where('computer_id', $data['Computer_ID'])
                    ->lockForUpdate()
                    ->first();

                if ($device) {
                    return [
                        'id'     => (int) $device->id,
                        'status' => 'exists',
                    ];
                }

                $isFirstDevice = ! LicenseDevice::query()
                    ->where('license_id', $licenseId)
                    ->exists();

                $device = LicenseDevice::query()->create([
                    'license_id'  => $licenseId,
                    'computer_id' => $data['Computer_ID'],
                    'bios_id'     => $data['Bios_ID'],
                    'disk_id'     => $data['Disk_ID'],
                    'base_id'     => $data['Base_ID'],
                    'video_id'    => $data['Video_ID'],
                    'mac_id'      => $data['Mac_ID'],
                    'is_primary'  => $isFirstDevice,
                    'device_name' => $isFirstDevice ? 'Main' : null,
                ]);

                return [
                    'id'     => (int) $device->id,
                    'status' => 'created',
                ];
            });

            return response()->json([
                'success' => true,
                'message' => 'Inserted',
                'data'    => [
                    'result' => $result['id'],
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => app()->isProduction() ? 'An error occurred. Please try again later.' : 'An error occurred: '.$exception->getMessage(),
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
            return response()->json(app()->isProduction() ? 'Internal server error.' : 'Internal server error: '.$exception->getMessage(), 500);
        }
    }

    private function generateSerialNumber(): string
    {
        do {
            $serial = 'SL-'.now()->format('ymd').'-'.Str::upper(Str::random(8));
        } while (License::query()->where('serial_number', $serial)->exists());

        return $serial;
    }

    public function getKey(Request $request): JsonResponse
    {
        $request->validate([
            'ComputerID' => 'required|string',
        ]);

        $key = LicenseDevice::query()
            ->where('computer_id', $request->input('ComputerID'))
            ->latest('id')
            ->value('license_key');

        if (! empty($key)) {
            return response()->json([
                'success' => true,
                'message' => 'Registered',
                'data'    => ['LicenseKey' => $key],
            ], 200);
        }

        return response()->json([
            'success' => false,
            'message' => 'Not Registered',
            'data'    => null,
        ], 200);
    }

    public function checkMail(Request $request): JsonResponse
    {
        $validated = $request->validate(['ComputerID' => ['required', 'string']]);
        $licenseId = $this->licenseIdForComputer($validated['ComputerID']);

        $active = $licenseId
            && LicenseSubscription::query()->where('license_id', $licenseId)
                ->ofType(ServiceType::Mail->value)->activeNow()->exists()
            && LicenseShiftEmail::query()->where('license_id', $licenseId)->exists();

        if (! $active) {
            return response()->json(['success' => true, 'message' => 'Not Active']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Active',
            'data'    => [
                'result'    => 1,
                'server'    => config('mail.mailers.smtp.host'),
                'email'     => config('mail.mailers.smtp.username') ?: config('mail.from.address'),
                'password'  => config('mail.mailers.smtp.password'),
                'helo_name' => config('mail.mailers.smtp.local_domain'),
                'status'    => config('mail.default') === 'smtp' ? 'True' : 'False',
            ],
        ]);
    }

    public function checkValid(Request $request): JsonResponse
    {
        $validated = $request->validate(['ComputerID' => ['required', 'string']]);
        $active = LicenseDevice::query()
            ->where('computer_id', $validated['ComputerID'])
            ->whereHas('license', fn ($query) => $query->where('is_active', true))
            ->exists();

        return response()->json([
            'success' => true,
            'message' => $active ? 'Active' : 'Not Active',
            'data'    => ['result' => $active ? 1 : 0],
        ]);
    }

    public function checkKey(Request $request): JsonResponse
    {
        $validated = $request->validate(['ComputerID' => ['required', 'string']]);
        $registered = LicenseDevice::query()->where('computer_id', $validated['ComputerID'])->exists();

        return response()->json([
            'success' => $registered,
            'message' => $registered ? 'Registered' : 'Not Registered',
        ]);
    }

    public function licensesInfo(Request $request): JsonResponse
    {
        $validated = $request->validate(['ComputerID' => ['required', 'string']]);
        $license = License::query()
            ->with(['program:id,name', 'subscriptions'])
            ->whereHas('devices', fn ($query) => $query->where('computer_id', $validated['ComputerID']))
            ->first();

        if (! $license) {
            return response()->json(['success' => false, 'message' => 'Not Registered', 'data' => null]);
        }

        $technicalSupport = $license->subscriptions
            ->firstWhere('service_type', ServiceType::TechnicalSupport->value);

        return response()->json([
            'success' => true,
            'message' => 'Registered',
            'data'    => [
                'LicenseID'   => $license->id,
                'ProductName' => $license->program?->name,
                'TechSupport' => optional($technicalSupport?->end_date)?->toDateString(),
                'LicenseType' => $license->license_plan?->value,
                'Company'     => $license->company_name,
                'Client'      => $license->partner_id,
            ],
        ]);
    }

    public function techSupportInfo(Request $request): JsonResponse
    {
        $validated = $request->validate(['ComputerID' => ['required', 'string']]);
        $license = License::query()
            ->with(['program:id,name', 'subscriptions'])
            ->whereHas('devices', fn ($query) => $query->where('computer_id', $validated['ComputerID']))
            ->first();

        if (! $license) {
            return response()->json(['success' => false, 'message' => 'Not Registered', 'data' => null]);
        }

        $technicalSupport = $license->subscriptions->firstWhere('service_type', ServiceType::TechnicalSupport->value);
        $mail = $license->subscriptions->firstWhere('service_type', ServiceType::Mail->value);

        return response()->json([
            'success' => true,
            'message' => 'Registered',
            'data'    => [
                'ApplicationName'    => $license->program?->name,
                'CompanyName'        => $license->company_name,
                'TechSupportEndDate' => optional($technicalSupport?->end_date)?->toDateString() ?? 'N/A',
                'MailEndDate'        => optional($mail?->end_date)?->toDateString() ?? 'N/A',
                'LicensesID'         => $license->id,
            ],
        ]);
    }

    private function licenseIdForComputer(string $computerId): ?int
    {
        $licenseId = LicenseDevice::query()->where('computer_id', $computerId)->value('license_id');

        return $licenseId ? (int) $licenseId : null;
    }
}
