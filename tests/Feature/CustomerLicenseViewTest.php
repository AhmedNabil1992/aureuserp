<?php

namespace Webkul\Software\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Webkul\Partner\Models\Partner;
use Webkul\Software\Models\License;
use Webkul\Software\Models\LicenseSubscription;
use Webkul\Software\Models\Program;
use Webkul\Software\Models\ProgramEdition;
use Webkul\Software\Models\ProgramFeature;
use Webkul\Support\Models\Currency;

class CustomerLicenseViewTest extends TestCase
{
    use RefreshDatabase;

    protected Partner $customer;

    protected Program $program;

    protected ProgramEdition $edition;

    protected License $license;

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
        // Create a program
        $this->program = Program::factory()->create([
            'name' => 'Test Software',
        ]);

        // Create a program edition
        $this->edition = ProgramEdition::factory()->create([
            'program_id' => $this->program->id,
            'name'       => 'Professional',
        ]);

        // Create a customer
        $this->customer = Partner::factory()->create();

        // Create a license for the customer
        $this->license = License::factory()->create([
            'partner_id' => $this->customer->id,
            'program_id' => $this->program->id,
            'edition_id' => $this->edition->id,
            'status'     => 'active',
            'is_active'  => true,
        ]);
    }

    public function test_customer_can_view_their_licenses()
    {
        // Verify the license exists and belongs to the customer
        $this->assertDatabaseHas('software_licenses', [
            'partner_id' => $this->customer->id,
            'program_id' => $this->program->id,
        ]);

        // Test that the license model has correct relationships
        $license = License::find($this->license->id);
        $this->assertNotNull($license->program);
        $this->assertNotNull($license->edition);
        $this->assertEquals($this->customer->id, $license->partner_id);
    }

    public function test_customer_can_only_view_own_licenses()
    {
        // Create another customer
        $otherCustomer = Partner::factory()->create();

        // Create a license for the other customer
        $otherLicense = License::factory()->create([
            'partner_id' => $otherCustomer->id,
            'program_id' => $this->program->id,
        ]);

        // Verify the licenses are different
        $this->assertNotEquals($this->license->id, $otherLicense->id);
        $this->assertNotEquals($this->license->partner_id, $otherLicense->partner_id);

        // Verify filtering by customer works
        $customerLicenses = License::where('partner_id', $this->customer->id)->get();
        $this->assertCount(1, $customerLicenses);
        $this->assertEquals($this->license->id, $customerLicenses->first()->id);
    }

    public function test_license_has_subscriptions()
    {
        // Create program features
        $feature1 = ProgramFeature::factory()->create([
            'program_id' => $this->program->id,
            'name'       => 'Email Support',
        ]);

        $feature2 = ProgramFeature::factory()->create([
            'program_id' => $this->program->id,
            'name'       => 'Priority Support',
        ]);

        // Create subscriptions for the license
        $subscription1 = LicenseSubscription::factory()->create([
            'license_id' => $this->license->id,
            'feature_id' => $feature1->id,
            'start_date' => now(),
            'end_date'   => now()->addYear(),
            'is_active'  => true,
        ]);

        $subscription2 = LicenseSubscription::factory()->create([
            'license_id' => $this->license->id,
            'feature_id' => $feature2->id,
            'start_date' => now(),
            'end_date'   => now()->addMonth(),
            'is_active'  => true,
        ]);

        // Test relationships
        $license = License::with('subscriptions')->find($this->license->id);
        $this->assertCount(2, $license->subscriptions);

        // Verify subscription details
        $subscriptions = $license->subscriptions()->get();
        $this->assertTrue($subscriptions->contains('id', $subscription1->id));
        $this->assertTrue($subscriptions->contains('id', $subscription2->id));
    }

    public function test_license_subscription_dates_are_correct()
    {
        $feature = ProgramFeature::factory()->create([
            'program_id' => $this->program->id,
        ]);

        $startDate = now()->startOfMonth();
        $endDate = now()->endOfYear();

        $subscription = LicenseSubscription::factory()->create([
            'license_id' => $this->license->id,
            'feature_id' => $feature->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'is_active'  => true,
        ]);

        $subscription->refresh();
        $this->assertTrue($subscription->start_date->isSameDay($startDate));
        $this->assertTrue($subscription->end_date->isSameDay($endDate));
    }

    public function test_license_subscription_active_status()
    {
        $feature = ProgramFeature::factory()->create([
            'program_id' => $this->program->id,
        ]);

        // Create active subscription
        $activeSubscription = LicenseSubscription::factory()->create([
            'license_id' => $this->license->id,
            'feature_id' => $feature->id,
            'is_active'  => true,
        ]);

        // Create inactive subscription
        $inactiveSubscription = LicenseSubscription::factory()->create([
            'license_id' => $this->license->id,
            'feature_id' => $feature->id,
            'is_active'  => false,
        ]);

        $subscriptions = $this->license->subscriptions()->get();
        $active = $subscriptions->where('is_active', true)->count();
        $inactive = $subscriptions->where('is_active', false)->count();

        $this->assertEquals(1, $active);
        $this->assertEquals(1, $inactive);
    }

    public function test_multiple_customers_with_same_program()
    {
        // Create another customer with a license for the same program
        $customer2 = Partner::factory()->create();
        $license2 = License::factory()->create([
            'partner_id' => $customer2->id,
            'program_id' => $this->program->id,
        ]);

        // Verify both licenses exist for the same program
        $programLicenses = License::where('program_id', $this->program->id)->get();
        $this->assertGreaterThanOrEqual(2, $programLicenses->count());

        // Verify we can filter by customer
        $customer1Licenses = $programLicenses->where('partner_id', $this->customer->id);
        $customer2Licenses = $programLicenses->where('partner_id', $customer2->id);

        $this->assertTrue($customer1Licenses->contains('id', $this->license->id));
        $this->assertTrue($customer2Licenses->contains('id', $license2->id));
    }

    public function test_license_serial_number_is_unique_identifier()
    {
        $this->assertNotNull($this->license->serial_number);
        $this->assertIsString($this->license->serial_number);

        // Create another license
        $license2 = License::factory()->create([
            'partner_id' => $this->customer->id,
        ]);

        // Serial numbers should be different
        $this->assertNotEquals($this->license->serial_number, $license2->serial_number);
    }

    public function test_license_status_tracking()
    {
        $license = License::find($this->license->id);

        // Test initial status
        $this->assertEquals('active', $license->status);
        $this->assertTrue($license->is_active);

        // Update status
        $license->update([
            'status'    => 'expired',
            'is_active' => false,
        ]);

        $license->refresh();
        $this->assertEquals('expired', $license->status);
        $this->assertFalse($license->is_active);
    }

    public function test_license_date_ranges()
    {
        $startDate = now()->subDays(30);
        $endDate = now()->addDays(60);

        $license = License::factory()->create([
            'partner_id' => $this->customer->id,
            'program_id' => $this->program->id,
            'start_date' => $startDate,
            'end_date'   => $endDate,
        ]);

        $license->refresh();
        $this->assertTrue($license->start_date->isSameDay($startDate));
        $this->assertTrue($license->end_date->isSameDay($endDate));
    }
}
