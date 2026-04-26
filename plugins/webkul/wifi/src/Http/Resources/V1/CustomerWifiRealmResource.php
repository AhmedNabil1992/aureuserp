<?php

namespace Webkul\Wifi\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWifiRealmResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'       => $this->id,
            'name'     => $this->name,
            'cloud_id' => $this->cloud_id,
        ];
    }
}
