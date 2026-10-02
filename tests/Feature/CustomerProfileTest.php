<?php

use App\Filament\Customer\Auth\EditProfile;
use Filament\Facades\Filament;
use Livewire\Livewire;
use Webkul\Partner\Models\Partner;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

require_once __DIR__.'/../../plugins/webkul/support/tests/Helpers/TestBootstrapHelper.php';

beforeEach(function () {
    Filament::setCurrentPanel(Filament::getPanel('customer'));
});

it('allows a customer to update their phone and address', function () {
    $customer = Partner::factory()->create();
    $country = Country::factory()->create();
    $state = State::factory()->create(['country_id' => $country->id]);
    $city = City::query()->create([
        'state_id' => $state->id,
        'name'     => 'Cairo',
    ]);

    $this->actingAs($customer, 'customer');

    Livewire::test(EditProfile::class)
        ->fillForm([
            'name'       => $customer->name,
            'email'      => $customer->email,
            'phone'      => '01012345678',
            'country_id' => $country->id,
            'state_id'   => $state->id,
            'city_id'    => $city->id,
            'street1'    => '10 Test Street',
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    expect($customer->refresh())
        ->phone->toBe('01012345678')
        ->country_id->toBe($country->id)
        ->state_id->toBe($state->id)
        ->city_id->toBe($city->id)
        ->street1->toBe('10 Test Street');
});

it('rejects a state and city outside the selected address hierarchy', function () {
    $customer = Partner::factory()->create();
    $country = Country::factory()->create();
    $otherCountry = Country::factory()->create();
    $otherState = State::factory()->create(['country_id' => $otherCountry->id]);
    $otherCity = City::query()->create([
        'state_id' => $otherState->id,
        'name'     => 'Other City',
    ]);

    $this->actingAs($customer, 'customer');

    Livewire::test(EditProfile::class)
        ->fillForm([
            'name'       => $customer->name,
            'email'      => $customer->email,
            'country_id' => $country->id,
            'state_id'   => $otherState->id,
            'city_id'    => $otherCity->id,
        ])
        ->call('save')
        ->assertHasFormErrors(['state_id']);
});
