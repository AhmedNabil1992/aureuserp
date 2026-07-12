<?php

namespace Webkul\Software\Http\Requests\API\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class LicenseActivityUpsertRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->hasAny([
            'CurrentVersion',
            'ComputerID',
        ])) {
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
            'CurrentVersion'  => ['required', 'string', 'max:50'],
            'ComputerID'      => ['required', 'string', 'max:255', 'exists:software_license_devices,computer_id'],
            'ApplicationName' => ['nullable', 'string', 'max:255'],
        ];
    }
}
