<?php

namespace Webkul\Website\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerFcmTokenRequest extends FormRequest
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
            'fcm_token'   => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }

    /**
     * @return array<string, array<string, mixed>>
     */
    public function bodyParameters(): array
    {
        return [
            'fcm_token' => [
                'description' => 'Firebase Cloud Messaging registration token for this device.',
                'example'     => 'dP7abcXyz123:APA91bH....',
            ],
            'device_name' => [
                'description' => 'Optional device label (android, ios, web).',
                'example'     => 'flutter-android',
            ],
        ];
    }
}
