<?php

namespace Webkul\Website\Http\Controllers\API\V1;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Laravel\Sanctum\PersonalAccessToken;
use Webkul\Software\Models\CustomerNotification;
use Webkul\Software\Models\FcmToken;
use Webkul\Software\Models\License;
use Webkul\Software\Services\FirebaseNotificationService;
use Webkul\Support\Models\City;
use Webkul\Website\Http\Requests\CustomerFcmTokenRequest;
use Webkul\Website\Http\Requests\CustomerLoginRequest;
use Webkul\Website\Http\Requests\CustomerRegisterRequest;
use Webkul\Website\Http\Resources\V1\CustomerResource;
use Webkul\Website\Models\Partner;
use Webkul\Wifi\Models\WifiPartnerCloud;

#[Group('Website API Management')]
#[Subgroup('Customer Authentication', 'Register and authenticate customer accounts for the mobile application')]
class CustomerAuthController extends Controller
{
    #[Endpoint('Register customer', 'Create a new customer account and require email verification before login.')]
    #[Unauthenticated]
    #[ResponseFromApiResource(CustomerResource::class, Partner::class, status: 201, additional: ['message' => 'Customer registered successfully. Please verify your email before login.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"email": ["The email has already been taken."]}}')]
    public function register(CustomerRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cityName = City::query()->whereKey($data['city_id'])->value('name');

        $customer = Partner::create([
            'name'          => $data['name'],
            'email'         => $data['email'],
            'phone'         => $data['phone'],
            'country_id'    => $data['country_id'],
            'state_id'      => $data['state_id'],
            'city'          => $cityName,
            'street1'       => $data['street1'],
            'password'      => $data['password'],
            'customer_rank' => 1,
            'is_active'     => true,
        ]);

        if ($customer instanceof MustVerifyEmail && ! $customer->hasVerifiedEmail()) {
            $customer->sendEmailVerificationNotification();
        }

        return response()->json([
            'message'            => 'تم تسجيل العميل بنجاح. يرجى التحقق من بريدك الإلكتروني قبل تسجيل الدخول.',
            'email_verification' => [
                'required' => true,
                'verified' => $customer->hasVerifiedEmail(),
            ],
            'data'               => CustomerResource::make($customer)->resolve(),
        ], 201);
    }

    #[Endpoint('Login customer', 'Authenticate a customer account and issue a Sanctum token.')]
    #[Unauthenticated]
    #[Response(status: 200, description: 'Login successful', content: '{"message": "Login successful.", "token": "1|abcd1234efgh5678ijkl", "token_type": "Bearer", "data": {"id": 1, "name": "Ahmed Ali", "email": "customer@example.com"}}')]
    #[Response(status: 422, description: 'Invalid credentials', content: '{"message": "The given data was invalid.", "errors": {"email": ["The provided credentials are incorrect."]}}')]
    public function login(CustomerLoginRequest $request): JsonResponse
    {
        $data = $request->validated();

        $customer = Partner::query()->where('email', $data['email'])->first();

        if (! $customer || ! $customer->password || ! Hash::check($data['password'], $customer->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        if ($customer instanceof MustVerifyEmail && ! $customer->hasVerifiedEmail()) {
            return response()->json([
                'message' => 'يرجي تأكيد البريد الإلكتروني الخاص بك قبل تسجيل الدخول.',
            ], 403);
        }

        $token = $customer->createToken($data['device_name'] ?? 'customer-mobile')->plainTextToken;

        $hasWifiVouchers = false;

        if (Schema::hasTable('wifi_partner_clouds')) {
            $hasWifiVouchers = WifiPartnerCloud::query()
                ->where('partner_id', $customer->id)
                ->exists();
        }

        $hasPlaystationService = false;

        if (Schema::hasTable('software_licenses') && Schema::hasTable('software_programs')) {
            $hasPlaystationService = License::query()
                ->where('partner_id', $customer->id)
                ->whereHas('program', function ($query): void {
                    $query->where('name', 'Playstation Time Management');
                })
                ->exists();
        }

        $customerData = CustomerResource::make($customer)->resolve();
        $customerData['services'] = [
            'wifi_vouchers' => $hasWifiVouchers,
            'playstation'   => $hasPlaystationService,
        ];

        return response()->json([
            'message'            => 'تم تسجيل الدخول بنجاح',
            'token'              => $token,
            'token_type'         => 'Bearer',
            'email_verification' => [
                'required' => true,
                'verified' => true,
            ],
            'data'               => $customerData,
        ]);
    }

    #[Endpoint('Current customer', 'Return the currently authenticated customer.')]
    #[Authenticated]
    #[ResponseFromApiResource(CustomerResource::class, Partner::class)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function me(Request $request): CustomerResource
    {
        return CustomerResource::make($request->user());
    }

    #[Endpoint('Update customer FCM token', 'Register or refresh Firebase Cloud Messaging token for the authenticated customer device.')]
    #[Authenticated]
    #[Response(status: 200, description: 'Token updated', content: '{"success": true, "message": "FCM Token updated successfully."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function updateFcmToken(CustomerFcmTokenRequest $request): JsonResponse
    {
        /** @var Partner $customer */
        $customer = $request->user();

        $token = $request->validated('fcm_token');

        FcmToken::updateOrCreate(
            ['token' => $token],
            [
                'user_id'     => null,
                'partner_id'  => $customer->id,
                'device_name' => $request->validated('device_name'),
            ],
        );

        $this->pushUnreadNotificationsToToken(
            customerId: (int) $customer->id,
            token: (string) $token,
        );

        return response()->json([
            'success' => true,
            'message' => 'FCM Token updated successfully.',
        ]);
    }

    #[Endpoint('Logout customer', 'Revoke the current customer access token.')]
    #[Authenticated]
    #[Response(status: 200, description: 'Logout successful', content: '{"message": "Logout successful."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function logout(Request $request): JsonResponse
    {
        /** @var Partner $customer */
        $customer = $request->user();

        $validated = $request->validate([
            'fcm_token' => ['nullable', 'string', 'max:255'],
        ]);

        if (! empty($validated['fcm_token'])) {
            FcmToken::query()
                ->where('partner_id', $customer->id)
                ->where('token', $validated['fcm_token'])
                ->delete();
        }

        $accessToken = $customer->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();
        }

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }

    private function pushUnreadNotificationsToToken(int $customerId, string $token): void
    {
        $notifications = CustomerNotification::query()
            ->where('partner_id', $customerId)
            ->where('is_read', false)
            ->latest('id')
            ->limit(20)
            ->get();

        if ($notifications->isEmpty()) {
            return;
        }

        $firebaseNotificationService = app(FirebaseNotificationService::class);

        foreach ($notifications as $notification) {
            $firebaseNotificationService->sendToTokens(
                tokens: [$token],
                title: $notification->title,
                body: $notification->body,
                data: $this->normalizeDataPayload($notification->data),
            );
        }
    }

    /**
     * @param  array<string, mixed>|null  $data
     * @return array<string, string>
     */
    private function normalizeDataPayload(?array $data): array
    {
        if (empty($data)) {
            return [];
        }

        return collect($data)
            ->filter(fn ($value, $key): bool => filled($key) && filled($value))
            ->mapWithKeys(function ($value, $key): array {
                if (is_scalar($value)) {
                    return [(string) $key => (string) $value];
                }

                return [(string) $key => json_encode($value, JSON_UNESCAPED_UNICODE) ?: ''];
            })
            ->filter(fn ($value): bool => $value !== '')
            ->all();
    }
}
