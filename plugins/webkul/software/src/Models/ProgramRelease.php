<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramRelease extends Model
{
    use HasFactory;

    protected $table = 'software_program_releases';

    protected $fillable = [
        'program_id',
        'version_number',
        'update_link',
        'release_date',
        'file_name',
        'app_terminate',
        'is_db_update',
        'db_link',
        'download_times',
        'is_active',
        'remark',
    ];

    protected $casts = [
        'release_date'   => 'date',
        'is_db_update'   => 'boolean',
        'download_times' => 'integer',
        'is_active'      => 'boolean',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }
}
