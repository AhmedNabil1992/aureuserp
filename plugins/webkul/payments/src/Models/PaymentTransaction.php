<?php

namespace Webkul\Payment\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Webkul\Payment\Database\Factories\PaymentTransactionFactory;

class PaymentTransaction extends Model
{
    use HasFactory;

    protected $table = 'payments_payment_transactions';

    protected $guarded = [];

    protected $casts = [
        'transaction_details' => 'array',
        'is_reconciled'       => 'boolean',
    ];

    protected static function newFactory(): Factory
    {
        return PaymentTransactionFactory::new();
    }
}
