<?php

namespace Webkul\Wifi\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerWifiGetRequest extends FormRequest
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
            'customer_id' => ['required', 'integer'],
        ];
    }
}
