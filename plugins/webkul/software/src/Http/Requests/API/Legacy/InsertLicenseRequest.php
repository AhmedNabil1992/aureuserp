<?php

namespace Webkul\Software\Http\Requests\API\Legacy;

use Illuminate\Foundation\Http\FormRequest;

class InsertLicenseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->keysContainLegacyPayload()) {
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
            'CompanyName' => ['required', 'string', 'max:255'],
            'ProductID'   => ['required', 'integer', 'exists:software_programs,id'],
            'ClientID'    => ['required', 'integer', 'exists:partners_partners,id'],
            'GoverID'     => ['nullable', 'integer', 'exists:states,id'],
            'CityID'      => ['nullable', 'integer', 'exists:cities,id'],
            'Address'     => ['nullable', 'string', 'max:255'],
            'LicenseType' => ['nullable', 'string'],
            'Period'      => ['nullable', 'integer', 'min:1'],
        ];
    }

    private function keysContainLegacyPayload(): bool
    {
        return $this->hasAny([
            'CompanyName',
            'ProductID',
            'ClientID',
            'GoverID',
            'CityID',
            'Address',
        ]);
    }
}
