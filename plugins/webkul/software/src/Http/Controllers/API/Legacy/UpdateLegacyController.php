<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Throwable;
use Webkul\Software\Enums\ServiceType;
use Webkul\Software\Http\Requests\API\Legacy\CheckForUpdateRequest;
use Webkul\Software\Http\Requests\API\Legacy\LicenseActivityUpsertRequest;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseActivity;
use Webkul\Software\Models\ProgramRelease;

class UpdateLegacyController extends Controller
{
    public function syncActivity(LicenseActivityUpsertRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $licenseId = $this->resolveLicenseId((string) $data['ComputerID']);

            if (! $licenseId) {
                return response()->json([
                    'success' => false,
                    'message' => 'License not found for the provided identifiers.',
                ], 422);
            }

            $activity = $this->logActivity($licenseId, (string) $data['CurrentVersion']);
            $license = License::query()
                ->with(['program:id,name', 'edition:id,name'])
                ->find($licenseId);

            return response()->json([
                'success' => true,
                'message' => 'License activity synced successfully.',
                'data'    => [
                    'license_id'      => $activity->license_id,
                    'license_serial'  => $license?->serial_number,
                    'program_name'    => $license?->program?->name,
                    'edition_name'    => $license?->edition?->name,
                    'current_version' => $activity->current_version,
                    'last_online_at'  => optional($activity->last_online_at)?->toDateTimeString(),
                ],
            ]);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => app()->isProduction() ? 'An error occurred. Please try again later.' : $exception->getMessage(),
            ], 500);
        }
    }

    public function checkForUpdate(CheckForUpdateRequest $request): JsonResponse
    {
        $data = $request->validated();

        try {
            $licenseId = $this->resolveLicenseId((string) $data['ComputerID']);

            if ($licenseId) {
                $this->logActivity($licenseId, (string) $data['CurrentVersion']);
            }

            if (! $licenseId || ! $this->hasActiveTechnicalSupportSubscription($licenseId)) {
                return response()->json([
                    'success' => true,
                    'message' => 'No updates available',
                    'data'    => [
                        'UpdateLink'    => 'No updates available',
                        'Filename'      => null,
                        'LatestVersion' => null,
                        'App_Terminate' => null,
                        'IsDBUpdate'    => null,
                        'DB_Link'       => null,
                    ],
                ], 200, [], JSON_UNESCAPED_SLASHES);
            }

            $releaseQuery = ProgramRelease::query()
                ->where('is_active', true)
                ->whereHas('program', function ($query) use ($data): void {
                    $query->where('name', $data['ApplicationName']);
                })
                ->get();

            $updateDetails = $releaseQuery
                ->filter(fn (ProgramRelease $release): bool => version_compare($release->version_number, $data['CurrentVersion'], '>'))
                ->sort(fn (ProgramRelease $a, ProgramRelease $b): int => version_compare($a->version_number, $b->version_number))
                ->first();

            if (! $updateDetails) {
                return response()->json([
                    'success' => true,
                    'message' => 'No updates available',
                    'data'    => [
                        'UpdateLink'    => 'No updates available',
                        'Filename'      => null,
                        'LatestVersion' => null,
                        'App_Terminate' => null,
                        'IsDBUpdate'    => null,
                        'DB_Link'       => null,
                    ],
                ], 200, [], JSON_UNESCAPED_SLASHES);
            }

            $updateDetails->increment('download_times');

            return response()->json([
                'success' => true,
                'message' => 'Update available',
                'data'    => [
                    'UpdateLink'    => $updateDetails->update_link,
                    'Filename'      => $updateDetails->file_name,
                    'LatestVersion' => $updateDetails->version_number,
                    'App_Terminate' => $updateDetails->app_terminate,
                    'IsDBUpdate'    => $updateDetails->is_db_update,
                    'DB_Link'       => $updateDetails->db_link,
                ],
            ], 200, [], JSON_UNESCAPED_SLASHES);
        } catch (Throwable $exception) {
            return response()->json([
                'success' => false,
                'message' => app()->isProduction() ? 'An error occurred while checking for updates.' : $exception->getMessage(),
            ], 500);
        }
    }

    private function resolveLicenseId(string $computerId): ?int
    {
        $resolvedId = (int) License::query()
            ->whereHas('devices', function ($query) use ($computerId): void {
                $query->where('computer_id', $computerId);
            })
            ->value('id');

        return $resolvedId > 0 ? $resolvedId : null;
    }

    private function hasActiveTechnicalSupportSubscription(int $licenseId): bool
    {
        return License::query()
            ->whereKey($licenseId)
            ->whereHas('subscriptions', function ($query): void {
                $query
                    ->ofType(ServiceType::TechnicalSupport->value)
                    ->activeNow();
            })
            ->exists();
    }

    private function logActivity(int $licenseId, string $currentVersion): LicenseActivity
    {
        return LicenseActivity::query()->create([
            'license_id'      => $licenseId,
            'current_version' => $currentVersion,
            'last_online_at'  => now(),
        ]);
    }
}
