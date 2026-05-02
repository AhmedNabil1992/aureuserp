<?php

namespace Webkul\Software\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Group;
use Webkul\Software\Http\Resources\V1\CustomerNotificationResource;
use Webkul\Software\Models\CustomerNotification;

#[Group('Customer Notifications')]
#[Authenticated]
class CustomerNotificationController extends Controller
{
    public function index(Request $request): ResourceCollection
    {
        $customer = $request->user();

        $notifications = CustomerNotification::query()
            ->where('partner_id', $customer->id)
            ->latest('id')
            ->paginate($request->integer('per_page', 20));

        return CustomerNotificationResource::collection($notifications);
    }

    public function markAsRead(Request $request, CustomerNotification $notification): JsonResponse
    {
        $customer = $request->user();

        abort_unless($notification->partner_id === $customer->id, 403);

        if (! $notification->is_read) {
            $notification->update([
                'is_read' => true,
                'read_at' => now(),
            ]);
        }

        return response()->json([
            'message' => 'Notification marked as read.',
        ]);
    }

    public function markAllAsRead(Request $request): JsonResponse
    {
        $customer = $request->user();

        CustomerNotification::query()
            ->where('partner_id', $customer->id)
            ->where('is_read', false)
            ->update([
                'is_read'    => true,
                'read_at'    => now(),
                'updated_at' => now(),
            ]);

        return response()->json([
            'message' => 'All notifications marked as read.',
        ]);
    }
}
