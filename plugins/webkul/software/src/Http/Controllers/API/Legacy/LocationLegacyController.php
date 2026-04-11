<?php

namespace Webkul\Software\Http\Controllers\API\Legacy;

use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller;
use Webkul\Software\Http\Requests\API\Legacy\CityLegacyRequest;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class LocationLegacyController extends Controller
{
    public function index(): JsonResponse
    {
        $egyptId = Country::query()->where('code', 'EG')->value('id');

        $governorates = State::query()
            ->when($egyptId, fn ($query) => $query->where('country_id', $egyptId))
            ->orderBy('name')
            ->get(['id', 'name', 'name_ar'])
            ->map(fn (State $state): array => [
                'id'          => $state->id,
                'governorate' => $state->name_ar ?: $state->name,
            ])
            ->values();

        return response()->json($governorates);
    }

    public function getCity(CityLegacyRequest $request): JsonResponse
    {
        $governorateId = (int) $request->validated('goverID');

        $cities = City::query()
            ->where('state_id', $governorateId)
            ->orderBy('name')
            ->get(['id', 'state_id', 'name'])
            ->map(fn (City $city): array => [
                'ID'       => $city->id,
                'Gover_ID' => $city->state_id,
                'City'     => $city->name,
            ])
            ->values();

        return response()->json($cities);
    }
}
