<?php

namespace Webkul\Software\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'ticket_number'    => $this->ticket_number,
            'title'            => $this->title,
            'content'          => $this->content,
            'status'           => $this->status,
            'priority'         => $this->priority,
            'is_unread_admin'  => $this->is_unread_admin,
            'is_unread_client' => $this->is_unread_client,
            'reopened'         => $this->reopened,
            'first_closed_at'  => $this->first_closed_at,
            'last_closed_at'   => $this->last_closed_at,
            'created_at'       => $this->created_at,
            'updated_at'       => $this->updated_at,
            'partner'          => $this->whenLoaded('partner', fn () => [
                'id'   => $this->partner->id,
                'name' => $this->partner->name,
            ]),
            'license'         => $this->whenLoaded('license', fn () => [
                'id'            => $this->license->id,
                'serial_number' => $this->license->serial_number,
            ]),
            'program'         => $this->whenLoaded('program', fn () => [
                'id'   => $this->program->id,
                'name' => $this->program->name,
            ]),
            'assigned_to'     => $this->whenLoaded('assignedTo', fn () => [
                'id'   => $this->assignedTo->id,
                'name' => $this->assignedTo->name,
            ]),
            'attachments'     => $this->whenLoaded('attachments', fn () => $this->attachments->map(fn ($a) => [
                'id'            => $a->id,
                'url'           => $a->url,
                'original_name' => $a->original_name,
                'mime_type'     => $a->mime_type,
                'file_size'     => $a->file_size,
            ])),
            'events'          => $this->whenLoaded('events', TicketEventResource::collection($this->events)),
        ];
    }
}
