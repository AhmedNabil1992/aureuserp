<?php

namespace Webkul\SoftwareOnline\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Account\Models\Move;
use Webkul\Partner\Models\Partner;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\TransactionType;

class OnlineInstanceTransaction extends Model
{
    protected $table = 'online_instance_transactions';

    protected $fillable = [
        'instance_id',
        'partner_id',
        'type',
        'billing_cycle',
        'amount',
        'status',
        'period_start',
        'period_end',
        'move_id',
        'move_line_id',
    ];

    protected $casts = [
        'type'          => TransactionType::class,
        'billing_cycle' => BillingCycle::class,
        'amount'        => 'decimal:2',
        'period_start'  => 'date',
        'period_end'    => 'date',
    ];

    public function instance(): BelongsTo
    {
        return $this->belongsTo(OnlineInstance::class, 'instance_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function move(): BelongsTo
    {
        return $this->belongsTo(Move::class, 'move_id');
    }

    public function moveLine(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Account\Models\MoveLine::class, 'move_line_id');
    }
}
