<?php

namespace Webkul\Account\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Webkul\Account\Models\BalanceRequest;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentTransaction;

/**
 * @extends Factory<BalanceRequest>
 */
class BalanceRequestFactory extends Factory
{
    protected $model = BalanceRequest::class;

    public function definition(): array
    {
        return [
            'partner_id'             => Partner::query()->value('id') ?? Partner::factory(),
            'payment_transaction_id' => PaymentTransaction::factory(),
            'amount'                 => fake()->randomFloat(2, 10, 5000),
            'status'                 => BalanceRequest::STATUS_PENDING,
            'requested_at'           => now(),
            'approved_at'            => null,
            'approved_by'            => null,
            'rejection_reason'       => null,
        ];
    }
}
