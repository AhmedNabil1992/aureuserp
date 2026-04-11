<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;

class TicketEvent extends Model
{
    use HasFactory;

    protected $table = 'software_ticket_events';

    protected $fillable = [
        'ticket_id',
        'user_id',
        'partner_id',
        'type',
        'content',
        'file_path',
        'is_private',
    ];

    protected $casts = [
        'is_private' => 'boolean',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }
}
