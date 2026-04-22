<?php

namespace Webkul\Support\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'name_ar'    => $this->name_ar,
            'state_id'   => $this->state_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'state'      => new StateResource($this->whenLoaded('state')),
        ];
    }
}
