<?php

declare(strict_types=1);

namespace Webkul\Vpn\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;
use Webkul\Security\Models\User;

class VpnServer extends Model
{
    protected $table = 'vpn_servers';

    protected $fillable = [
        'name',
        'host',
        'port',
        'admin_password',
        'verify_tls',
        'timeout_seconds',
        'is_active',
        'known_hubs',
        'last_connected_at',
        'last_failed_at',
        'last_error',
        'creator_id',
    ];

    protected $hidden = ['admin_password'];

    protected function casts(): array
    {
        return [
            'admin_password'    => 'encrypted',
            'verify_tls'        => 'boolean',
            'timeout_seconds'   => 'integer',
            'is_active'         => 'boolean',
            'known_hubs'        => 'array',
            'last_connected_at' => 'datetime',
            'last_failed_at'    => 'datetime',
        ];
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function endpoint(): string
    {
        $host = trim($this->host);
        $host = preg_replace('#^https?://#i', '', $host) ?? $host;
        $host = rtrim($host, '/');

        return "https://{$host}:{$this->port}/api/";
    }

    public function getKnownHubsDisplayAttribute(): string
    {
        return implode(', ', $this->known_hubs ?? []);
    }

    protected static function booted(): void
    {
        static::creating(function (self $server): void {
            $server->creator_id ??= Auth::id();
        });
    }
}
