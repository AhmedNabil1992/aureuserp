<?php

namespace Webkul\Website\Http\Controllers\API\V1;

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Routing\Controller;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\Unauthenticated;
use Webkul\Support\Http\Resources\V1\CityResource;
use Webkul\Support\Http\Resources\V1\CountryResource;
use Webkul\Support\Http\Resources\V1\StateResource;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;
use Webkul\Website\Http\Requests\CustomerCitiesListRequest;
use Webkul\Website\Http\Requests\CustomerStatesListRequest;

#[Group('Website API Management')]
#[Subgroup('Customer Locations', 'Public endpoints for selecting country, state, and city during registration')]
class CustomerLocationController extends Controller
{
    #[Endpoint('List countries', 'Return all available countries for customer registration.')]
    #[Unauthenticated]
    #[ResponseFromApiResource(CountryResource::class, Country::class, collection: true)]
    #[Response(status: 200, description: 'Countries list loaded')]
    public function countries(): AnonymousResourceCollection
    {
        $countries = Country::query()
            ->orderBy('name')
            ->get();

        return CountryResource::collection($countries);
    }

    #[Endpoint('List states by country', 'Return states for a selected country ID.')]
    #[Unauthenticated]
    #[QueryParam('country_id', 'int', 'Selected country ID.', required: true, example: 1)]
    #[ResponseFromApiResource(StateResource::class, State::class, collection: true)]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"country_id": ["The selected country id is invalid."]}}')]
    public function states(CustomerStatesListRequest $request): AnonymousResourceCollection
    {
        $states = State::query()
            ->where('country_id', $request->validated('country_id'))
            ->orderBy('name')
            ->get();

        return StateResource::collection($states);
    }

    #[Endpoint('List cities by state', 'Return cities for a selected state ID.')]
    #[Unauthenticated]
    #[QueryParam('state_id', 'int', 'Selected state ID.', required: true, example: 1)]
    #[ResponseFromApiResource(CityResource::class, City::class, collection: true)]
    #[Response(status: 422, description: 'Validation error', content: '{"message": "The given data was invalid.", "errors": {"state_id": ["The selected state id is invalid."]}}')]
    public function cities(CustomerCitiesListRequest $request): AnonymousResourceCollection
    {
        $cities = City::query()
            ->where('state_id', $request->validated('state_id'))
            ->orderBy('name')
            ->get();

        return CityResource::collection($cities);
    }
}
