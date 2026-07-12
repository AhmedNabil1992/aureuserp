<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgramEditionFeature extends Model
{
    use HasFactory;

    protected $table = 'software_program_edition_features';

    protected $fillable = [
        'program_edition_id',
        'program_feature_id',
        'price',
        'auto_attach_on_final_license',
        'is_complimentary',
        'included_duration_days',
        'invoice_on_initial_billing',
        'invoice_on_renewal',
        'auto_renew_with_license',
        'sort_order',
    ];

    protected $casts = [
        'price'                        => 'decimal:2',
        'auto_attach_on_final_license' => 'boolean',
        'is_complimentary'             => 'boolean',
        'included_duration_days'       => 'integer',
        'invoice_on_initial_billing'   => 'boolean',
        'invoice_on_renewal'           => 'boolean',
        'auto_renew_with_license'      => 'boolean',
        'sort_order'                   => 'integer',
    ];

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ProgramEdition::class, 'program_edition_id');
    }

    public function feature(): BelongsTo
    {
        return $this->belongsTo(ProgramFeature::class, 'program_feature_id');
    }
}
