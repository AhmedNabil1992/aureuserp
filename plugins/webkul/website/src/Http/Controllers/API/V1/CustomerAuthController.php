<?php

namespace Webkul\Website\Http\Controllers\API\V1;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Laravel\Sanctum\PersonalAccessToken;
use Webkul\Support\Models\City;
use Webkul\Website\Http\Requests\CustomerLoginRequest;
use Webkul\Website\Http\Requests\CustomerRegisterRequest;
use Webkul\Website\Http\Resources\V1\CustomerResource;
use Webkul\Website\Models\Partner;

#[Group('Website API Management')]
#[Subgroup('Customer Authentication', 'Register and authenticate customer accounts for the mobile application')]
class CustomerAuthController extends Controller
{
    #[Endpoint('Register customer', 'Create a new customer account and issue a Sanctum token.')]
    #[Unauthenticated]
    #[ResponseFromApiResource(CustomerResource::class, Partner::class, status: 201, additional: ['message' => 'Customer registered successfully.'])]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"email": ["The email has already been taken."]}}')]
    public function register(CustomerRegisterRequest $request): JsonResponse
    {
        $data = $request->validated();
        $cityName = City::query()->whereKey($data['city_id'])->value('name');

        $customer = Partner::create([
            'name'       => $data['name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'],
            'country_id' => $data['country_id'],
            'state_id'   => $data['state_id'],
            'city'       => $cityName,
            'street1'    => $data['street1'],
            'password'   => $data['password'],
            'is_active'  => true,
        ]);

        $token = $customer->createToken($data['device_name'] ?? 'customer-mobile')->plainTextToken;

        return response()->json([
            'message'    => 'Customer registered successfully.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'data'       => CustomerResource::make($customer)->resolve(),
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

        $token = $customer->createToken($data['device_name'] ?? 'customer-mobile')->plainTextToken;

        return response()->json([
            'message'    => 'Login successful.',
            'token'      => $token,
            'token_type' => 'Bearer',
            'data'       => CustomerResource::make($customer)->resolve(),
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

    #[Endpoint('Logout customer', 'Revoke the current customer access token.')]
    #[Authenticated]
    #[Response(status: 200, description: 'Logout successful', content: '{"message": "Logout successful."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function logout(Request $request): JsonResponse
    {
        /** @var Partner $customer */
        $customer = $request->user();

        $accessToken = $customer->currentAccessToken();

        if ($accessToken instanceof PersonalAccessToken) {
            $accessToken->delete();
        }

        return response()->json([
            'message' => 'Logout successful.',
        ]);
    }
}
