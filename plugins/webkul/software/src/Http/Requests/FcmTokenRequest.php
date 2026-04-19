<?php

namespace Webkul\Software\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FcmTokenRequest extends FormRequest
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
            'token'       => ['required', 'string', 'max:255'],
            'device_name' => ['nullable', 'string', 'max:100'],
        ];
    }
}
