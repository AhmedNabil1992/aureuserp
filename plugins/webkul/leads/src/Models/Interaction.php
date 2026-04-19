<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Lead\Enums\InteractionType;
use Webkul\Security\Models\User;

class Interaction extends Model
{
    use HasFactory;

    protected $table = 'leads_interactions';

    protected $fillable = [
        'lead_id',
        'user_id',
        'type',
        'subject',
        'notes',
        'interaction_date',
        'outcome',
        'next_action',
        'follow_up_date',
    ];

    public function casts(): array
    {
        return [
            'type'             => InteractionType::class,
            'interaction_date' => 'datetime',
            'follow_up_date'   => 'datetime',
        ];
    }

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Lead::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($interaction) {
            $interaction->user_id ??= Auth::id();
            $interaction->interaction_date ??= now();
        });
    }
}
