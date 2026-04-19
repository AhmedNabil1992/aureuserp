<?php

namespace Webkul\Website\Models;

use Laravel\Sanctum\HasApiTokens;
use Webkul\Partner\Models\Partner as BasePartner;
use Webkul\Website\Database\Factories\PartnerFactory;

class Partner extends BasePartner
{
    use HasApiTokens;

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Create a new Eloquent model instance.
     *
     * @return void
     */
    public function __construct(array $attributes = [])
    {
        $this->mergeFillable([
            'password',
            'is_active',
        ]);

        $this->mergeCasts([
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
        ]);

        parent::__construct($attributes);
    }

    protected static function newFactory(): PartnerFactory
    {
        return PartnerFactory::new();
    }
}
