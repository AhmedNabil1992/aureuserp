<?php

namespace Webkul\Software\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerNotificationResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'title'      => $this->title,
            'body'       => $this->body,
            'data'       => $this->data,
            'is_read'    => $this->is_read,
            'read_at'    => $this->read_at,
            'created_at' => $this->created_at,
        ];
    }
}
