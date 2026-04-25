<?php

namespace Webkul\Wifi\Http\Resources\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CustomerWifiDynamicClientResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $firstRealm = $this->whenLoaded('dynamicClientRealms', function () {
            return $this->dynamicClientRealms->first()?->realm;
        });

        return [
            'id'              => (int) $this->id,
            'name'            => $this->name,
            'nasidentifier'   => $this->nasidentifier,
            'cloud_id'        => (int) $this->cloud_id,
            'realm_id'        => $firstRealm?->id,
            'realm_name'      => $firstRealm?->name,
            'last_contact'    => $this->last_contact,
            'last_contact_ip' => $this->last_contact_ip,
            'active'          => (bool) $this->active,
            'picture'         => $this->Picture,
            'zero_ip'         => $this->zero_ip,
            'created'         => $this->created,
            'modified'        => $this->modified,
        ];
    }
}
