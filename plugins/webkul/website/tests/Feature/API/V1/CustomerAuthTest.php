<?php

use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Website\Models\Partner;

require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    TestBootstrapHelper::ensureERPInstalled();

    if (! Schema::hasColumn('partners_partners', 'password') || ! Schema::hasTable('website_pages')) {
        Artisan::call('migrate', [
            '--path'  => 'plugins/webkul/website/database/migrations',
            '--force' => true,
        ]);

        Artisan::call('db:seed', [
            '--class' => 'Webkul\\Website\\Database\\Seeders\\DatabaseSeeder',
            '--force' => true,
        ]);
    }

    if (! Route::has('customer.api.v1.website.auth.register')) {
        require base_path('plugins/webkul/website/routes/api.php');
    }
});

function customerAuthRoute(string $action): string
{
    return route("customer.api.v1.website.auth.{$action}");
}

/**
 * @return array{country: Country, state: State, city: City}
 */
function createAddressData(): array
{
    $country = Country::factory()->create();

    $state = State::factory()->create([
        'country_id' => $country->id,
    ]);

    $city = City::query()->create([
        'state_id' => $state->id,
        'name'     => 'Test City '.uniqid(),
    ]);

    return [
        'country' => $country,
        'state'   => $state,
        'city'    => $city,
    ];
}

function createCustomer(array $overrides = []): Partner
{
    $address = createAddressData();

    return Partner::query()->create(array_merge([
        'name'       => 'Test Customer',
        'email'      => 'customer.'.uniqid().'@example.com',
        'phone'      => '+201001234567',
        'country_id' => $address['country']->id,
        'state_id'   => $address['state']->id,
        'city'       => $address['city']->name,
        'street1'    => 'Test Street 1',
        'password'   => 'password123',
        'is_active'  => true,
        'creator_id' => null,
    ], $overrides));
}

it('registers a customer and returns a bearer token', function () {
    /** @var TestCase $this */
    $address = createAddressData();

    $payload = [
        'name'                  => 'Mobile Customer',
        'email'                 => 'mobile.customer@example.com',
        'phone'                 => '+201001234567',
        'country_id'            => $address['country']->id,
        'state_id'              => $address['state']->id,
        'city_id'               => $address['city']->id,
        'street1'               => 'Nasr City - Street 10',
        'password'              => 'password123',
        'password_confirmation' => 'password123',
        'device_name'           => 'flutter-ios',
    ];

    $response = $this->postJson(customerAuthRoute('register'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Customer registered successfully.')
        ->assertJsonPath('data.email', $payload['email']);

    expect($response->json('token'))->not->toBeEmpty();

    $this->assertDatabaseHas('partners_partners', [
        'email'      => $payload['email'],
        'name'       => $payload['name'],
        'phone'      => $payload['phone'],
        'country_id' => $payload['country_id'],
        'state_id'   => $payload['state_id'],
        'city'       => $address['city']->name,
        'street1'    => $payload['street1'],
    ]);
});

it('lists countries for registration', function () {
    /** @var TestCase $this */
    Country::factory()->create([
        'name' => 'Egypt',
        'code' => 'EG',
    ]);

    $this->getJson(customerAuthRoute('locations.countries'))
        ->assertOk()
        ->assertJsonPath('data.0.code', 'EG');
});

it('lists states by country id for registration', function () {
    /** @var TestCase $this */
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);

    $this->getJson(customerAuthRoute('locations.states').'?country_id='.$country->id)
        ->assertOk()
        ->assertJsonPath('data.0.id', $state->id);
});

it('lists cities by state id for registration', function () {
    /** @var TestCase $this */
    $state = State::factory()->create();
    $city = City::query()->create([
        'state_id' => $state->id,
        'name'     => 'Test City For API '.uniqid(),
    ]);

    $this->getJson(customerAuthRoute('locations.cities').'?state_id='.$state->id)
        ->assertOk()
        ->assertJsonPath('data.0.id', $city->id);
});

it('logs in a customer with valid credentials', function () {
    /** @var TestCase $this */
    $customer = createCustomer([
        'email' => 'login.customer@example.com',
    ]);

    $response = $this->postJson(customerAuthRoute('login'), [
        'email'       => $customer->email,
        'password'    => 'password123',
        'device_name' => 'flutter-android',
    ]);

    $response
        ->assertOk()
        ->assertJsonPath('message', 'Login successful.')
        ->assertJsonPath('data.id', $customer->id);

    expect($response->json('token'))->not->toBeEmpty();
});

it('rejects invalid customer credentials', function () {
    /** @var TestCase $this */
    createCustomer([
        'email' => 'invalid.customer@example.com',
    ]);

    $this->postJson(customerAuthRoute('login'), [
        'email'    => 'invalid.customer@example.com',
        'password' => 'wrong-password',
    ])
        ->assertUnprocessable()
        ->assertJsonValidationErrors(['email']);
});

it('requires authentication to fetch the current customer', function () {
    /** @var TestCase $this */
    $this->getJson(customerAuthRoute('me'))->assertUnauthorized();
});

it('returns the authenticated customer profile and revokes the current token on logout', function () {
    /** @var TestCase $this */
    $customer = createCustomer([
        'email' => 'profile.customer@example.com',
    ]);

    $token = $customer->createToken('test-device')->plainTextToken;

    $this->withToken($token)
        ->getJson(customerAuthRoute('me'))
        ->assertOk()
        ->assertJsonPath('data.email', $customer->email);

    $this->withToken($token)
        ->postJson(customerAuthRoute('logout'))
        ->assertOk()
        ->assertJsonPath('message', 'Logout successful.');

    expect($customer->tokens()->count())->toBe(0);
});
