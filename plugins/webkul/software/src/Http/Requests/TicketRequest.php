<?php

namespace Webkul\Software\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;

class TicketRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $isUpdate = $this->isMethod('PUT') || $this->isMethod('PATCH');

        return [
            'partner_id'    => $isUpdate ? ['sometimes', 'required', 'integer', 'exists:partners_partners,id'] : ['required', 'integer', 'exists:partners_partners,id'],
            'license_id'    => ['nullable', 'integer', 'exists:software_licenses,id'],
            'program_id'    => ['nullable', 'integer', 'exists:software_programs,id'],
            'assigned_to'   => ['nullable', 'integer', 'exists:users,id'],
            'title'         => $isUpdate ? ['sometimes', 'required', 'string', 'max:255'] : ['required', 'string', 'max:255'],
            'content'       => $isUpdate ? ['sometimes', 'required', 'string'] : ['required', 'string'],
            'priority'      => ['nullable', Rule::enum(TicketPriority::class)],
            'status'        => $isUpdate ? ['sometimes', 'required', Rule::enum(TicketStatus::class)] : ['nullable', Rule::enum(TicketStatus::class)],
            'attachments'   => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'partner_id'  => ['description' => 'ID of the customer partner.', 'example' => 1],
            'license_id'  => ['description' => 'ID of the software license (optional).', 'example' => 5],
            'program_id'  => ['description' => 'ID of the software program (optional).', 'example' => 2],
            'assigned_to' => ['description' => 'Admin user ID to assign the ticket to.', 'example' => 3],
            'title'       => ['description' => 'Short subject of the ticket.', 'example' => 'Cannot activate license'],
            'content'     => ['description' => 'Full description of the issue (HTML allowed).', 'example' => '<p>After renewal, the activation fails.</p>'],
            'priority'    => ['description' => 'Ticket priority: low, normal, high, urgent.', 'example' => 'high'],
            'status'      => ['description' => 'Ticket status: open, pending, closed.', 'example' => 'open'],
            'attachments' => ['description' => 'Optional file attachments (max 10 MB each).'],
        ];
    }
}
