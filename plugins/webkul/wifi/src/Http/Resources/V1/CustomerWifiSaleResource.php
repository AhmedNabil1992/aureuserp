<?php

namespace Webkul\Wifi\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWifiSaleResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'cloud_id'      => $this->cloudID,
            'cloud_name'    => $this->cloud?->name,
            'nasidentifier' => $this->nasidentifier,
            'sales_count'   => $this->SCount,
            'sold_at'       => $this->Date?->toIso8601String(),
        ];
    }
}
