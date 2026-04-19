<?php

namespace Webkul\Software\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Webkul\Software\Http\Requests\FcmTokenRequest;
use Webkul\Software\Models\FcmToken;

class FcmTokenController extends Controller
{
    /**
     * Register or refresh an FCM token for the authenticated user/customer.
     * The token is upserted so the same device never accumulates duplicates.
     */
    public function store(FcmTokenRequest $request): JsonResponse
    {
        $data = $this->resolveOwner($request);

        FcmToken::updateOrCreate(
            ['token' => $request->validated('token')],
            array_merge($data, [
                'device_name' => $request->validated('device_name'),
            ]),
        );

        return response()->json(['message' => 'FCM token registered.'], 200);
    }

    /**
     * Remove a specific FCM token (e.g., on logout / unsubscribe).
     */
    public function destroy(Request $request): JsonResponse
    {
        $request->validate(['token' => ['required', 'string']]);

        FcmToken::where('token', $request->input('token'))->delete();

        return response()->json(['message' => 'FCM token removed.'], 200);
    }

    /**
     * Determine if the caller is an admin (user) or a customer (partner)
     * and return the correct owner column array.
     *
     * @return array<string, int|null>
     */
    private function resolveOwner(Request $request): array
    {
        if (Auth::guard('sanctum')->check()) {
            return ['user_id' => Auth::guard('sanctum')->id(), 'partner_id' => null];
        }

        if (Auth::guard('customer')->check()) {
            return ['user_id' => null, 'partner_id' => Auth::guard('customer')->id()];
        }

        return ['user_id' => null, 'partner_id' => null];
    }
}
