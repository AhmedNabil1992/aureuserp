<?php

namespace Webkul\Software\Http\Requests\API\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class LicenseInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->hasAny(['Computer_ID', 'ProductKey'])) {
            return;
        }

        $rawContent = trim((string) $this->getContent());

        if ($rawContent === '') {
            return;
        }

        $decodedJson = json_decode($rawContent, true);

        if (is_array($decodedJson) && $decodedJson !== []) {
            $this->merge($decodedJson);

            return;
        }

        parse_str($rawContent, $parsedBody);

        if (is_array($parsedBody) && $parsedBody !== []) {
            unset($parsedBody['Content-Type']);

            $this->merge($parsedBody);
        }
    }

    public function rules(): array
    {
        return [
            'Computer_ID' => ['required', 'string', 'max:255'],
            'ProductKey'  => ['required', 'string', 'max:255'],
        ];
    }
}
