<?php

namespace Webkul\Support\Http\Controllers\API\V1;

use Illuminate\Support\Facades\Gate;
use Knuckles\Scribe\Attributes\Authenticated;
use Knuckles\Scribe\Attributes\Endpoint;
use Knuckles\Scribe\Attributes\Group;
use Knuckles\Scribe\Attributes\QueryParam;
use Knuckles\Scribe\Attributes\Response;
use Knuckles\Scribe\Attributes\ResponseFromApiResource;
use Knuckles\Scribe\Attributes\Subgroup;
use Knuckles\Scribe\Attributes\UrlParam;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\QueryBuilder;
use Webkul\Support\Http\Resources\V1\CityResource;
use Webkul\Support\Models\City;

#[Group('Support API Management')]
#[Subgroup('Cities', 'Browse cities')]
#[Authenticated]
class CityController extends Controller
{
    #[Endpoint('List cities', 'Retrieve a paginated list of cities with filtering and sorting')]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> state, state.country', required: false, example: 'state')]
    #[QueryParam('filter[id]', 'string', 'Comma-separated list of IDs to filter by', required: false, example: 'No-example')]
    #[QueryParam('filter[name]', 'string', 'Filter by city name (partial match)', required: false, example: 'No-example')]
    #[QueryParam('filter[state_id]', 'int', 'Filter by state ID', required: false, example: 'No-example')]
    #[QueryParam('sort', 'string', 'Sort field', example: 'name')]
    #[QueryParam('page', 'int', 'Page number', example: 1)]
    #[ResponseFromApiResource(CityResource::class, City::class, collection: true, paginate: 10)]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function index()
    {
        Gate::authorize('viewAny', City::class);

        $cities = QueryBuilder::for(City::class)
            ->allowedFilters([
                AllowedFilter::exact('id'),
                AllowedFilter::partial('name'),
                AllowedFilter::exact('state_id'),
            ])
            ->allowedSorts(['id', 'name', 'created_at'])
            ->allowedIncludes([
                'state',
                'state.country',
            ])
            ->paginate();

        return CityResource::collection($cities);
    }

    #[Endpoint('Show city', 'Retrieve a specific city by its ID')]
    #[UrlParam('id', 'integer', 'The city ID', required: true, example: 1)]
    #[QueryParam('include', 'string', 'Comma-separated list of relationships to include. </br></br><b>Available options:</b> state, state.country', required: false, example: 'state')]
    #[ResponseFromApiResource(CityResource::class, City::class)]
    #[Response(status: 404, description: 'City not found', content: '{"message": "Resource not found."}')]
    #[Response(status: 401, description: 'Unauthenticated', content: '{"message": "Unauthenticated."}')]
    public function show(string $id)
    {
        $city = QueryBuilder::for(City::where('id', $id))
            ->allowedIncludes([
                'state',
                'state.country',
            ])
            ->firstOrFail();

        Gate::authorize('view', $city);

        return new CityResource($city);
    }
}
