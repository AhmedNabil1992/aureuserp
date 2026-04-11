<?php

namespace Webkul\Software\Http\Requests\API\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class ClientIdRequest extends FormRequest
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
            'email' => ['required', 'email'],
        ];
    }
}
