<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\Software\Http\Requests\API\Legacy\ClientIdRequest;
use Webkul\Software\Models\License;
use Webkul\Website\Models\Partner;

class ClientLegacyController extends Controller
{
    public function getClientId(ClientIdRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $client = Partner::query()
            ->where('email', $validated['email'])
            ->whereNotNull('email_verified_at')
            ->where('is_active', true)
            ->first();

        if (! $client) {
            return response()->json([
                'success' => false,
                'message' => 'العميل غير مسجل او لم يتم تأكيد البريد الإلكتروني',
                'data'    => null,
            ], 404);
        }

        $licenses = License::query()
            ->where('partner_id', $client->id)
            ->get()
            ->map(fn (License $license): array => [
                'ID'           => $license->id,
                'Company_Name' => $license->company_name,
                'Address'      => $license->address,
                'ProductID'    => $license->program_id,
            ])
            ->values();

        return response()->json([
            'success' => true,
            'message' => 'العميل مسجل وتم تأكيد البريد الإلكتروني',
            'data'    => [
                'client_id' => $client->id,
                'license'   => $licenses,
            ],
        ]);
    }
}
