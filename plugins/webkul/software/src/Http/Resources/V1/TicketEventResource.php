<?php

namespace Webkul\Software\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketEventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'ticket_id'  => $this->ticket_id,
            'type'       => $this->type,
            'content'    => $this->content,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
            'sender'     => $this->when($this->user_id, fn () => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
                'type' => 'staff',
            ])) ?? $this->whenLoaded('partner', fn () => [
                'id'   => $this->partner?->id,
                'name' => $this->partner?->name,
                'type' => 'customer',
            ]),
            'attachments' => $this->whenLoaded('attachments', fn () => $this->attachments->map(fn ($a) => [
                'id'            => $a->id,
                'url'           => $a->url,
                'original_name' => $a->original_name,
                'mime_type'     => $a->mime_type,
                'file_size'     => $a->file_size,
            ])),
        ];
    }
}
