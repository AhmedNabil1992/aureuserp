<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Software\Enums\ServiceType;

class ProgramFeature extends Model
{
    use HasFactory;

    protected $table = 'software_program_features';

    protected $fillable = [
        'program_id',
        'name',
        'service_type',
        'description',
        'amount',
    ];

    protected $casts = [
        'amount'       => 'decimal:2',
        'service_type' => ServiceType::class,
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
