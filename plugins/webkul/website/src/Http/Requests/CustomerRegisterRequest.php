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
            'phone'                 => ['required', 'string', 'max:255'],
            'country_id'            => ['required', 'integer', 'exists:countries,id'],
            'state_id'              => ['required', 'integer', Rule::exists('states', 'id')->where(fn ($query) => $query->where('country_id', $this->input('country_id')))],
            'city_id'               => ['required', 'integer', Rule::exists('cities', 'id')->where(fn ($query) => $query->where('state_id', $this->input('state_id')))],
            'street1'               => ['required', 'string', 'max:255'],
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
            'phone' => [
                'description' => 'Customer phone number.',
                'example'     => '+201001234567',
            ],
            'country_id' => [
                'description' => 'Selected country ID.',
                'example'     => 65,
            ],
            'state_id' => [
                'description' => 'Selected state ID. Must belong to the selected country.',
                'example'     => 1524,
            ],
            'city_id' => [
                'description' => 'Selected city ID. Must belong to the selected state.',
                'example'     => 32012,
            ],
            'street1' => [
                'description' => 'Street address line 1.',
                'example'     => 'Nasr City - Street 10',
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
