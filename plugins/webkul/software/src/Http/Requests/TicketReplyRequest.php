<?php

namespace Webkul\Software\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TicketReplyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content'       => ['required', 'string'],
            'is_private'    => ['nullable', 'boolean'],
            'attachments'   => ['nullable', 'array'],
            'attachments.*' => ['file', 'max:10240'],
        ];
    }

    public function bodyParameters(): array
    {
        return [
            'content'     => ['description' => 'Reply message content (HTML allowed).', 'example' => '<p>We have fixed the issue.</p>'],
            'is_private'  => ['description' => 'Whether this reply is an internal note (admin only).', 'example' => false],
            'attachments' => ['description' => 'Optional file attachments (max 10 MB each).'],
        ];
    }
}
