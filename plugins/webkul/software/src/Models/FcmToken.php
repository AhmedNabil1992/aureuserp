<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Partner\Models\Partner;
use Webkul\Security\Models\User;

/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $partner_id
 * @property string $token
 * @property string|null $device_name
 */
class FcmToken extends Model
{
    protected $table = 'fcm_tokens';

    protected $fillable = [
        'user_id',
        'partner_id',
        'token',
        'device_name',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function partner(): BelongsTo
    {
        return $this->belongsTo(Partner::class);
    }
}
