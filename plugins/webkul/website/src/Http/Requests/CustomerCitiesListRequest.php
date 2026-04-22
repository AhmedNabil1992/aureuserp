<?php

namespace Webkul\Website\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CustomerCitiesListRequest extends FormRequest
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
            'state_id' => ['required', 'integer', 'exists:states,id'],
        ];
    }
}
