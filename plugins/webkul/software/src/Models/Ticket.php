<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Software\Enums\TicketPriority;
use Webkul\Software\Enums\TicketStatus;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'software_tickets';

    protected $fillable = [
        'ticket_number',
        'program_id',
        'license_id',
        'partner_id',
        'assigned_to',
        'creator_id',
        'title',
        'content',
        'file_path',
        'status',
        'priority',
        'is_unread_admin',
        'is_unread_client',
        'reopened',
        'first_closed_at',
        'last_closed_at',
        'closed_by',
    ];

    protected $casts = [
        'status'           => TicketStatus::class,
        'priority'         => TicketPriority::class,
        'is_unread_admin'  => 'boolean',
        'is_unread_client' => 'boolean',
        'reopened'         => 'boolean',
        'first_closed_at'  => 'datetime',
        'last_closed_at'   => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function closer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class, 'ticket_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'software_ticket_tag', 'ticket_id', 'tag_id')
            ->withTimestamps();
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'software_ticket_assignees', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(TicketAttachment::class, 'attachable');
    }
}
