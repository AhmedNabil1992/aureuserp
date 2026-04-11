<?php

namespace Webkul\Software\Http\Requests\API\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class InsertKeysRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->hasAny([
            'License_ID',
            'Computer_ID',
            'Bios_ID',
            'Disk_ID',
            'Base_ID',
            'Video_ID',
            'Mac_ID',
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
            'License_ID'  => ['required', 'integer', 'exists:software_licenses,id'],
            'Computer_ID' => ['required', 'string', 'max:255'],
            'Bios_ID'     => ['required', 'string', 'max:255'],
            'Disk_ID'     => ['required', 'string', 'max:255'],
            'Base_ID'     => ['required', 'string', 'max:255'],
            'Video_ID'    => ['required', 'string', 'max:255'],
            'Mac_ID'      => ['required', 'string', 'max:255'],
        ];
    }
}
