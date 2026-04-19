<?php

namespace Webkul\Lead\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;
use Webkul\Chatter\Traits\HasChatter;
use Webkul\Chatter\Traits\HasLogActivity;
use Webkul\Lead\Enums\LeadSource;
use Webkul\Lead\Enums\LeadStatus;
use Webkul\Lead\Enums\LeadTemperature;
use Webkul\Marketing\Models\Campaign;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class Lead extends Model
{
    use HasChatter, HasFactory, HasLogActivity, SoftDeletes;

    protected $table = 'leads_leads';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'company_name',
        'service_id',
        'status',
        'source',
        'temperature',
        'notes',
        'assigned_to',
        'campaign_id',
        'creator_id',
    ];

    public function casts(): array
    {
        return [
            'status'      => LeadStatus::class,
            'source'      => LeadSource::class,
            'temperature' => LeadTemperature::class,
        ];
    }

    public function getModelTitle(): string
    {
        return $this->name;
    }

    public function getLogAttributeLabels(): array
    {
        return [
            'name'         => 'Name',
            'phone'        => 'Phone',
            'email'        => 'Email',
            'company_name' => 'Company',
            'service_id'   => 'Service',
            'status'       => 'Status',
            'source'       => 'Source',
            'temperature'  => 'Temperature',
            'assigned_to'  => 'Assigned To',
            'campaign_id'  => 'Campaign',
            'notes'        => 'Notes',
        ];
    }

    public function interactions(): HasMany
    {
        return $this->hasMany(Interaction::class);
    }

    public function assignedUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function campaign(): BelongsTo
    {
        return $this->belongsTo(Campaign::class, 'campaign_id');
    }

    public function service(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'service_id');
    }

    public function scopePendingFollowUp($query)
    {
        return $query->whereHas('interactions', function ($q) {
            $q->whereDate('follow_up_date', today());
        });
    }

    public function scopeInactive($query, int $days = 7)
    {
        return $query->where(function ($q) use ($days) {
            $q->whereDoesntHave('interactions')
                ->orWhereHas('interactions', function ($iq) use ($days) {
                    $iq->where('interaction_date', '<', now()->subDays($days))
                        ->whereNotIn('id', function ($sub) {
                            $sub->selectRaw('MAX(id)')
                                ->from('leads_interactions')
                                ->groupBy('lead_id');
                        });
                });
        });
    }

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function ($lead) {
            $lead->creator_id ??= Auth::id();
        });
    }
}
