<?php

namespace Webkul\Wifi\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\URL;
use Throwable;
use Webkul\Wifi\Http\Requests\CustomerWifiAccessRequest;
use Webkul\Wifi\Http\Requests\CustomerWifiGetRequest;
use Webkul\Wifi\Http\Resources\V1\CustomerWifiCloudResource;
use Webkul\Wifi\Http\Resources\V1\CustomerWifiDynamicClientResource;
use Webkul\Wifi\Http\Resources\V1\CustomerWifiRealmResource;
use Webkul\Wifi\Http\Resources\V1\CustomerWifiSaleResource;
use Webkul\Wifi\Http\Resources\V1\CustomerWifiVoucherBatchResource;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\DynamicClient;
use Webkul\Wifi\Models\Realm;
use Webkul\Wifi\Models\VoucherSale;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Wifi\Models\WifiPurchase;
use Webkul\Wifi\Models\WifiVoucherBatch;

class CustomerWifiController extends Controller
{
    public function clouds(CustomerWifiAccessRequest $request): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        if (empty($cloudIds) || ! $this->canQueryMariadbTable('clouds')) {
            return response()->json([
                'message' => 'Clouds fetched successfully.',
                'data'    => [],
            ]);
        }

        $clouds = Cloud::query()
            ->whereIn('id', $cloudIds)
            ->orderBy('id')
            ->get();

        return response()->json([
            'message' => 'Clouds fetched successfully.',
            'data'    => CustomerWifiCloudResource::collection($clouds)->resolve(),
        ]);
    }

    public function dynamicClients(CustomerWifiAccessRequest $request): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        if (empty($cloudIds) || ! $this->canQueryMariadbTable('dynamic_clients')) {
            return response()->json([
                'message' => 'Dynamic clients fetched successfully.',
                'data'    => [],
            ]);
        }

        $dynamicClients = DynamicClient::query()
            ->with('dynamicClientRealms.realm')
            ->whereIn('cloud_id', $cloudIds)
            ->orderBy('id')
            ->get();

        return response()->json([
            'message' => 'Dynamic clients fetched successfully.',
            'data'    => CustomerWifiDynamicClientResource::collection($dynamicClients)->resolve(),
        ]);
    }

    public function cloudRealms(CustomerWifiGetRequest $request, int $cloudId): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerGetAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        if (! in_array($cloudId, $cloudIds, true)) {
            return response()->json([
                'message' => 'هذا السحاب لا يخص هذا العميل.',
            ], 403);
        }

        if (! $this->canQueryMariadbTable('realms')) {
            return response()->json([
                'message' => 'Realms fetched successfully.',
                'data'    => [],
            ]);
        }

        $realms = Realm::query()
            ->where('cloud_id', $cloudId)
            ->orderBy('id')
            ->get();

        return response()->json([
            'message' => 'Realms fetched successfully.',
            'data'    => CustomerWifiRealmResource::collection($realms)->resolve(),
        ]);
    }

    public function dashboard(CustomerWifiGetRequest $request): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerGetAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        $totalClouds = count($cloudIds);
        $totalAccessPoints = 0;
        $activeAccessPoints = 0;
        $lastContactAt = null;
        $totalVouchers = 0;
        $usedVouchers = 0;
        $totalSales = 0;

        if (! empty($cloudIds)) {
            if ($this->canQueryMariadbTable('dynamic_clients')) {
                $apQuery = DynamicClient::query()->whereIn('cloud_id', $cloudIds);
                $totalAccessPoints = $apQuery->count();
                $activeAccessPoints = (clone $apQuery)->where('active', true)->count();
                $lastContact = (clone $apQuery)->orderByDesc('last_contact')->value('last_contact');

                if ($lastContact) {
                    $lastContactAt = $lastContact instanceof \DateTimeInterface
                        ? $lastContact->format('Y-m-d\TH:i:s')
                        : $lastContact;
                }
            }

            if (Schema::hasTable('wifi_voucher_batches')) {
                $totalVouchers = WifiVoucherBatch::query()
                    ->whereIn('cloud_id', $cloudIds)
                    ->sum('quantity');
            }

            if ($this->canQueryMariadbTable('sales')) {
                $totalSales = (int) VoucherSale::query()
                    ->whereIn('cloudID', $cloudIds)
                    ->sum('SCount');

                $usedVouchers = $totalSales;
            }
        }

        return response()->json([
            'message' => 'Dashboard fetched successfully.',
            'data'    => [
                'total_clouds'         => $totalClouds,
                'total_access_points'  => $totalAccessPoints,
                'active_access_points' => $activeAccessPoints,
                'last_contact_at'      => $lastContactAt,
                'total_vouchers'       => (int) $totalVouchers,
                'used_vouchers'        => $usedVouchers,
                'remaining_vouchers'   => max(0, (int) $totalVouchers - $usedVouchers),
            ],
        ]);
    }

    public function voucherBatches(CustomerWifiGetRequest $request): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerGetAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        if (empty($cloudIds) || ! Schema::hasTable('wifi_voucher_batches')) {
            return response()->json([
                'message' => 'Voucher batches fetched successfully.',
                'data'    => [],
            ]);
        }

        $batches = WifiVoucherBatch::query()
            ->whereIn('cloud_id', $cloudIds)
            ->orderByDesc('id')
            ->get();

        return response()->json([
            'message' => 'Voucher batches fetched successfully.',
            'data'    => CustomerWifiVoucherBatchResource::collection($batches)->resolve(),
        ]);
    }

    public function voucherBatchDownloadUrl(CustomerWifiGetRequest $request, int $batchId): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerGetAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        if (! Schema::hasTable('wifi_voucher_batches')) {
            return response()->json(['message' => 'Batch not found.'], 404);
        }

        $batch = WifiVoucherBatch::query()
            ->whereIn('cloud_id', $cloudIds)
            ->find($batchId);

        if (! $batch) {
            return response()->json(['message' => 'Batch not found.'], 404);
        }

        return response()->json([
            'message' => 'Download URL fetched successfully.',
            'data'    => [
                'batch_id'   => $batch->id,
                'batch_code' => $batch->batch_code,
                'url'        => URL::temporarySignedRoute(
                    'wifi.voucher-batches.signed-download',
                    now()->addHour(),
                    ['batchCode' => $batch->batch_code]
                ),
            ],
        ]);
    }

    public function sales(CustomerWifiGetRequest $request): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerGetAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        if (empty($cloudIds) || ! $this->canQueryMariadbTable('sales')) {
            return response()->json([
                'message' => 'Sales fetched successfully.',
                'data'    => [],
            ]);
        }

        $query = VoucherSale::query()
            ->with('cloud')
            ->whereIn('cloudID', $cloudIds)
            ->orderByDesc('Date');

        if ($request->filled('cloud_id')) {
            $filterCloudId = (int) $request->input('cloud_id');

            if (in_array($filterCloudId, $cloudIds, true)) {
                $query->where('cloudID', $filterCloudId);
            }
        }

        $sales = $query->get();

        return response()->json([
            'message' => 'Sales fetched successfully.',
            'data'    => CustomerWifiSaleResource::collection($sales)->resolve(),
        ]);
    }

    public function salesSummary(CustomerWifiGetRequest $request): JsonResponse
    {
        $accessErrorResponse = $this->validateCustomerGetAccess($request);

        if ($accessErrorResponse instanceof JsonResponse) {
            return $accessErrorResponse;
        }

        $cloudIds = $this->resolveCustomerCloudIds($request->integer('customer_id'));

        $totalSales = 0;
        $remainingVouchers = 0;

        if (! empty($cloudIds)) {
            if ($this->canQueryMariadbTable('sales')) {
                $totalSales = (int) VoucherSale::query()
                    ->whereIn('cloudID', $cloudIds)
                    ->sum('SCount');
            }

            if (Schema::hasTable('wifi_purchases')) {
                $remainingVouchers = (int) WifiPurchase::query()
                    ->whereIn('cloud_id', $cloudIds)
                    ->sum('remaining_quantity');
            }
        }

        return response()->json([
            'message' => 'Sales summary fetched successfully.',
            'data'    => [
                'total_sales'        => $totalSales,
                'remaining_vouchers' => $remainingVouchers,
            ],
        ]);
    }

    /**
     * @return array<int, int>
     */
    private function resolveCustomerCloudIds(int $customerId): array
    {
        if (! Schema::hasTable('wifi_partner_clouds')) {
            return [];
        }

        return WifiPartnerCloud::query()
            ->where('partner_id', $customerId)
            ->pluck('cloud_id')
            ->map(fn ($cloudId): int => (int) $cloudId)
            ->unique()
            ->values()
            ->all();
    }

    private function validateCustomerAccess(CustomerWifiAccessRequest $request): ?JsonResponse
    {
        $authenticatedCustomer = $request->user();

        if (! $authenticatedCustomer) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ((int) $authenticatedCustomer->id !== $request->integer('customer_id')) {
            return response()->json([
                'message' => 'هذا التوكين لا يخص هذا العميل.',
            ], 403);
        }

        if ($request->bearerToken() !== $request->string('token')->toString()) {
            return response()->json([
                'message' => 'قيمة التوكين المرسلة لا تطابق توكين الجلسة.',
            ], 403);
        }

        return null;
    }

    private function validateCustomerGetAccess(CustomerWifiGetRequest $request): ?JsonResponse
    {
        $authenticatedCustomer = $request->user();

        if (! $authenticatedCustomer) {
            return response()->json([
                'message' => 'Unauthenticated.',
            ], 401);
        }

        if ((int) $authenticatedCustomer->id !== $request->integer('customer_id')) {
            return response()->json([
                'message' => 'هذا التوكين لا يخص هذا العميل.',
            ], 403);
        }

        return null;
    }

    private function canQueryMariadbTable(string $table): bool
    {
        try {
            return Schema::connection('mariadb')->hasTable($table);
        } catch (Throwable) {
            return false;
        }
    }
}
