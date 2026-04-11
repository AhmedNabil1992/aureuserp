<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Software\Enums\LicensePlan;
use Webkul\Software\Enums\LicenseStatus;
use Webkul\Support\Models\City;
use Webkul\Support\Models\State;

class License extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'software_licenses';

    protected $fillable = [
        'serial_number',
        'program_id',
        'edition_id',
        'partner_id',
        'state_id',
        'city_id',
        'address',
        'company_name',
        'license_plan',
        'status',
        'period',
        'start_date',
        'end_date',
        'is_active',
        'server_ip',
        'request_source',
        'requested_at',
        'approved_by',
        'creator_id',
    ];

    protected $casts = [
        'license_plan'   => LicensePlan::class,
        'status'         => LicenseStatus::class,
        'period'         => 'integer',
        'start_date'     => 'date',
        'end_date'       => 'date',
        'is_active'      => 'boolean',
        'requested_at'   => 'datetime',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ProgramEdition::class, 'edition_id');
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class, 'partner_id');
    }

    public function state(): BelongsTo
    {
        return $this->belongsTo(State::class, 'state_id');
    }

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class, 'city_id');
    }

    public function approver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function devices(): HasMany
    {
        return $this->hasMany(LicenseDevice::class, 'license_id');
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(LicenseSubscription::class, 'license_id');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(LicenseInvoice::class, 'license_id');
    }

    public function remoteProfile(): HasOne
    {
        return $this->hasOne(RemoteProfile::class, 'license_id');
    }

    public function activities(): HasMany
    {
        return $this->hasMany(LicenseActivity::class, 'license_id');
    }

    public function latestActivity(): HasOne
    {
        return $this->hasOne(LicenseActivity::class, 'license_id')->latestOfMany('last_online_at');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'license_id');
    }
}
