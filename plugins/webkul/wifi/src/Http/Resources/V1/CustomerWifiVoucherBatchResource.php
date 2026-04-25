<?php

namespace Webkul\Wifi\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWifiVoucherBatchResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'batch_code'    => $this->batch_code,
            'quantity'      => $this->quantity,
            'cloud_id'      => $this->cloud_id,
            'realm_id'      => $this->realm_id,
            'profile_id'    => $this->profile_id,
            'days_valid'    => $this->days_valid,
            'hours_valid'   => $this->hours_valid,
            'minutes_valid' => $this->minutes_valid,
            'never_expire'  => (bool) $this->never_expire,
            'caption'       => $this->caption,
            'created_at'    => $this->created_at?->toIso8601String(),
            'updated_at'    => $this->updated_at?->toIso8601String(),
        ];
    }
}
