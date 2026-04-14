<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Webkul\Product\Models\Product;
use Webkul\Security\Models\User;

class Program extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'software_programs';

    protected $fillable = [
        'name',
        'description',
        'slug',
        'product_id',
        'installation_notes',
        'is_active',
        'creator_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function editions(): HasMany
    {
        return $this->hasMany(ProgramEdition::class, 'program_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function features(): HasMany
    {
        return $this->hasMany(ProgramFeature::class, 'program_id');
    }

    public function releases(): HasMany
    {
        return $this->hasMany(ProgramRelease::class, 'program_id');
    }

    public function licenses(): HasMany
    {
        return $this->hasMany(License::class, 'program_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'program_id');
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creator_id');
    }
}
