<?php

namespace Webkul\Software\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Security\Models\User;
use Webkul\Software\Enums\LicensePlan;

class LicenseInvoice extends Model
{
    use HasFactory;

    protected $table = 'software_license_invoices';

    protected $fillable = [
        'license_id',
        'program_id',
        'edition_id',
        'license_plan',
        'invoice_number',
        'item_name',
        'quantity',
        'unit_price',
        'amount',
        'billed_by',
        'billed_at',
        'notes',
    ];

    protected $casts = [
        'license_plan' => LicensePlan::class,
        'quantity'     => 'decimal:2',
        'unit_price'   => 'decimal:2',
        'amount'       => 'decimal:2',
        'billed_at'    => 'datetime',
    ];

    public function license(): BelongsTo
    {
        return $this->belongsTo(License::class, 'license_id');
    }

    public function program(): BelongsTo
    {
        return $this->belongsTo(Program::class, 'program_id');
    }

    public function edition(): BelongsTo
    {
        return $this->belongsTo(ProgramEdition::class, 'edition_id');
    }

    public function billedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'billed_by');
    }
}
