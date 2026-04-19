<?php

namespace Webkul\Website\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerLoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, array<int, string>>
     */
    public function rules(): array
    {
        return [
            'email'       => ['required', 'email', 'max:255'],
            'password'    => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'email' => [
                'description' => 'Customer email address.',
                'example'     => 'customer@example.com',
            ],
            'password' => [
                'description' => 'Customer password.',
                'example'     => 'password123',
            ],
            'device_name' => [
                'description' => 'Optional device label used as the Sanctum token name.',
                'example'     => 'flutter-android',
            ],
        ];
    }
}
