<?php

namespace Webkul\Wifi\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Partner\Models\Partner;

class WifiPartnerCloud extends Model
{
    protected $table = 'wifi_partner_clouds';

    protected $fillable = [
        'partner_id',
        'cloud_id',
    ];

    public function casts(): array
    {
        return [
            'partner_id' => 'integer',
            'cloud_id'   => 'integer',
        ];
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function cloud(): BelongsTo
    {
        return $this->belongsTo(Cloud::class, 'cloud_id', 'id');
    }
}
