<?php

namespace Webkul\Website\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'name'              => $this->name,
            'email'             => $this->email,
            'phone'             => $this->phone,
            'mobile'            => $this->mobile,
            'country_id'        => $this->country_id,
            'state_id'          => $this->state_id,
            'city'              => $this->city,
            'street1'           => $this->street1,
            'avatar_url'        => $this->avatar_url,
            'is_active'         => (bool) $this->is_active,
            'email_verified_at' => $this->email_verified_at,
            'created_at'        => $this->created_at,
            'updated_at'        => $this->updated_at,
        ];
    }
}
