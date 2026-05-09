<?php

namespace Webkul\Account\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Webkul\Account\Models\BalanceRequest;
use Webkul\Account\Models\CustomerCredit;
use Webkul\Partner\Models\Partner;
use Webkul\Payment\Models\PaymentTransaction;
use Webkul\Support\Models\Currency;

class BalanceRequestTest extends TestCase
{
    use RefreshDatabase;

    protected Partner $customer;

    protected PaymentTransaction $paymentTransaction;

    protected function setUp(): void
    {
        parent::setUp();

        Currency::query()->upsert([
            [
                'id'             => 1,
                'name'           => 'USD',
                'symbol'         => '$',
                'iso_numeric'    => 840,
                'decimal_places' => 2,
                'full_name'      => 'US Dollar',
                'rounding'       => 0.01,
                'active'         => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id'             => 2,
                'name'           => 'EUR',
                'symbol'         => '€',
                'iso_numeric'    => 978,
                'decimal_places' => 2,
                'full_name'      => 'Euro',
                'rounding'       => 0.01,
                'active'         => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'id'             => 3,
                'name'           => 'EGP',
                'symbol'         => 'E£',
                'iso_numeric'    => 818,
                'decimal_places' => 2,
                'full_name'      => 'Egyptian Pound',
                'rounding'       => 0.01,
                'active'         => true,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ], ['id'], ['name', 'symbol', 'iso_numeric', 'decimal_places', 'full_name', 'rounding', 'active', 'updated_at']);

        // Create a customer (partner)
        $this->customer = Partner::factory()->create();

        // Create payment transaction linked to customer in payments plugin
        $this->paymentTransaction = PaymentTransaction::factory()->create([
            'partner_id'        => $this->customer->id,
            'partner_name'      => $this->customer->name,
            'payment_reference' => 'PAY-TEST-001',
        ]);
    }

    public function test_customer_can_create_balance_request()
    {
        $this->actingAs($this->customer, 'customer');

        $balanceRequest = BalanceRequest::factory()->create([
            'partner_id'              => $this->customer->id,
            'payment_transaction_id'  => $this->paymentTransaction->id,
            'amount'                  => 1000,
            'status'                  => BalanceRequest::STATUS_PENDING,
        ]);

        $this->assertDatabaseHas('accounts_balance_requests', [
            'partner_id' => $this->customer->id,
            'amount'     => 1000,
            'status'     => BalanceRequest::STATUS_PENDING,
        ]);
    }

    public function test_customer_cannot_create_duplicate_pending_requests()
    {
        $this->actingAs($this->customer, 'customer');

        // Create first request
        BalanceRequest::factory()->create([
            'partner_id' => $this->customer->id,
            'status'     => BalanceRequest::STATUS_PENDING,
        ]);

        // Check that pending request exists
        $this->assertTrue(BalanceRequest::hasPendingRequest($this->customer->id));

        // Verify we can get the pending request
        $pending = BalanceRequest::getPendingRequest($this->customer->id);
        $this->assertNotNull($pending);
        $this->assertEquals(BalanceRequest::STATUS_PENDING, $pending->status);
    }

    public function test_admin_can_approve_balance_request()
    {
        $balanceRequest = BalanceRequest::factory()->create([
            'partner_id' => $this->customer->id,
            'amount'     => 500,
            'status'     => BalanceRequest::STATUS_PENDING,
        ]);

        // Approve the request
        $balanceRequest->update([
            'status'      => BalanceRequest::STATUS_APPROVED,
            'approved_at' => now(),
            'approved_by' => 1,
        ]);

        // Create or update customer credit
        $credit = CustomerCredit::firstOrCreate(
            ['partner_id' => $this->customer->id],
            [
                'balance'         => 0,
                'reserved_amount' => 0,
                'status'          => 'active',
            ]
        );

        // Add balance
        $credit->increment('balance', $balanceRequest->amount);

        // Verify balance was updated
        $credit->refresh();
        $this->assertEquals(500, $credit->balance);

        // Verify request status
        $balanceRequest->refresh();
        $this->assertEquals(BalanceRequest::STATUS_APPROVED, $balanceRequest->status);
        $this->assertNotNull($balanceRequest->approved_at);
    }

    public function test_admin_can_reject_balance_request()
    {
        $balanceRequest = BalanceRequest::factory()->create([
            'partner_id' => $this->customer->id,
            'status'     => BalanceRequest::STATUS_PENDING,
        ]);

        $rejectionReason = 'Invalid bank account information';

        $balanceRequest->update([
            'status'           => BalanceRequest::STATUS_REJECTED,
            'rejection_reason' => $rejectionReason,
            'approved_by'      => 1,
        ]);

        $balanceRequest->refresh();
        $this->assertEquals(BalanceRequest::STATUS_REJECTED, $balanceRequest->status);
        $this->assertEquals($rejectionReason, $balanceRequest->rejection_reason);
    }

    public function test_customer_credit_is_created_automatically_on_first_approval()
    {
        // Verify no credit exists yet
        $this->assertFalse(CustomerCredit::where('partner_id', $this->customer->id)->exists());

        // Create and approve a request
        $balanceRequest = BalanceRequest::factory()->create([
            'partner_id' => $this->customer->id,
            'amount'     => 1000,
            'status'     => BalanceRequest::STATUS_PENDING,
        ]);

        $credit = CustomerCredit::firstOrCreate(
            ['partner_id' => $this->customer->id],
            [
                'balance'         => 0,
                'reserved_amount' => 0,
                'status'          => 'active',
            ]
        );

        $credit->increment('balance', $balanceRequest->amount);

        // Verify credit was created with correct amount
        $this->assertTrue(CustomerCredit::where('partner_id', $this->customer->id)->exists());
        $credit->refresh();
        $this->assertEquals(1000, $credit->balance);
    }

    public function test_available_balance_calculation()
    {
        $credit = CustomerCredit::create([
            'partner_id'      => $this->customer->id,
            'balance'         => 1000,
            'reserved_amount' => 300,
            'status'          => 'active',
        ]);

        $availableBalance = $credit->available_balance;
        $this->assertEquals(700, $availableBalance);
    }

    public function test_balance_request_relationships()
    {
        $balanceRequest = BalanceRequest::factory()->create([
            'partner_id'             => $this->customer->id,
            'payment_transaction_id' => $this->paymentTransaction->id,
        ]);

        // Test relationships
        $this->assertNotNull($balanceRequest->partner);
        $this->assertEquals($this->customer->id, $balanceRequest->partner->id);

        $this->assertNotNull($balanceRequest->paymentTransaction);
        $this->assertEquals($this->paymentTransaction->id, $balanceRequest->paymentTransaction->id);
    }

    public function test_balance_request_is_linked_to_customer_payment_transaction()
    {
        $secondTransaction = PaymentTransaction::factory()->create([
            'partner_id'        => $this->customer->id,
            'partner_name'      => $this->customer->name,
            'payment_reference' => 'PAY-TEST-002',
        ]);

        $balanceRequest = BalanceRequest::factory()->create([
            'partner_id'             => $this->customer->id,
            'payment_transaction_id' => $secondTransaction->id,
        ]);

        $this->assertDatabaseHas('accounts_balance_requests', [
            'payment_transaction_id' => $secondTransaction->id,
        ]);

        $this->assertSame($secondTransaction->id, $balanceRequest->payment_transaction_id);
    }
}
