<?php

namespace Webkul\Website\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Webkul\Website\Models\Partner;

class CustomerRegisterRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, mixed>>
     */
    public function rules(): array
    {
        return [
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email', 'max:255', Rule::unique((new Partner)->getTable(), 'email')],
            'password'              => ['required', 'confirmed', Password::default()],
            'password_confirmation' => ['required', 'string'],
            'device_name'           => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'name' => [
                'description' => 'Customer display name.',
                'example'     => 'Ahmed Ali',
            ],
            'email' => [
                'description' => 'Customer email address.',
                'example'     => 'customer@example.com',
            ],
            'password' => [
                'description' => 'Customer password.',
                'example'     => 'password123',
            ],
            'password_confirmation' => [
                'description' => 'Password confirmation.',
                'example'     => 'password123',
            ],
            'device_name' => [
                'description' => 'Optional device label used as the Sanctum token name.',
                'example'     => 'flutter-ios',
            ],
        ];
    }
}
