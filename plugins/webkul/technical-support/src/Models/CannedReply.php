<?php

namespace Webkul\TechnicalSupport\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\TechnicalSupport\Enums\ServiceType;

class CannedReply extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'technical_support_canned_replies';

    protected $fillable = [
        'title',
        'shortcut',
        'content',
        'service_type',
        'is_active',
    ];

    protected $casts = [
        'service_type' => ServiceType::class,
        'is_active'    => 'boolean',
    ];
}
