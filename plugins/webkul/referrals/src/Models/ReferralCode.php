<?php

declare(strict_types=1);

namespace Webkul\Referral\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;

class ReferralCode extends Model
{
    protected $table = 'referral_codes';

    protected $fillable = [
        'company_id',
        'partner_id',
        'code',
        'is_active',
        'creator_id',
    ];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function redemptions(): HasMany
    {
        return $this->hasMany(ReferralRedemption::class);
    }

    protected static function booted(): void
    {
        static::creating(function (self $referralCode): void {
            $referralCode->creator_id ??= Auth::guard('web')->id();
            $referralCode->code = filled($referralCode->code)
                ? Str::upper(trim($referralCode->code))
                : static::generateUniqueCode();
        });

        static::saving(function (self $referralCode): void {
            $referralCode->code = Str::upper(trim($referralCode->code));
        });
    }

    public static function generateUniqueCode(): string
    {
        do {
            $code = 'REF-'.Str::upper(Str::random(8));
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }
}
