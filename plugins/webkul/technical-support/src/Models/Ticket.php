<?php

namespace Webkul\TechnicalSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\TechnicalSupport\Enums\ServiceType;
use Webkul\TechnicalSupport\Enums\TicketPriority;
use Webkul\TechnicalSupport\Enums\TicketStatus;

class Ticket extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'technical_support_tickets';

    protected $fillable = [
        'ticket_number',
        'partner_id',
        'service_type',
        'program_id',
        'license_id',
        'cloud_id',
        'service_item_type',
        'service_item_id',
        'assigned_to',
        'creator_id',
        'closed_by',
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
    ];

    protected $casts = [
        'service_type'     => ServiceType::class,
        'status'           => TicketStatus::class,
        'priority'         => TicketPriority::class,
        'is_unread_admin'  => 'boolean',
        'is_unread_client' => 'boolean',
        'reopened'         => 'boolean',
        'first_closed_at'  => 'datetime',
        'last_closed_at'   => 'datetime',
    ];

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

    public function program(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Software\Models\Program::class, 'program_id');
    }

    public function license(): BelongsTo
    {
        return $this->belongsTo(\Webkul\Software\Models\License::class, 'license_id');
    }

    public function serviceItem(): MorphTo
    {
        return $this->morphTo();
    }

    public function events(): HasMany
    {
        return $this->hasMany(TicketEvent::class, 'ticket_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class, 'technical_support_ticket_tag', 'ticket_id', 'tag_id')
            ->withTimestamps();
    }

    public function assignees(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'technical_support_ticket_assignees', 'ticket_id', 'user_id')
            ->withTimestamps();
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(TicketAttachment::class, 'attachable');
    }

    public function getServiceLabelAttribute(): string
    {
        if ($this->service_type === ServiceType::Software) {
            $programName = $this->program?->name ?? 'Software';
            $serial = $this->license?->serial_number ? " ({$this->license->serial_number})" : '';
            return "{$programName}{$serial}";
        }

        if ($this->service_type === ServiceType::Wifi) {
            return __('technical-support::enums/service-type.wifi');
        }

        return $this->service_type?->getLabel() ?? 'General Support';
    }
}
