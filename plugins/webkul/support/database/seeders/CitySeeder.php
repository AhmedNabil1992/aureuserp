<?php

namespace Webkul\Support\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $countriesPath = base_path('plugins/webkul/security/src/Data/countries.json');
        $statesPath = base_path('plugins/webkul/security/src/Data/states.json');
        $combinedPath = base_path('countries+states+cities.json');

        if (! File::exists($countriesPath) || ! File::exists($statesPath) || ! File::exists($combinedPath)) {
            return;
        }

        $countries = json_decode(File::get($countriesPath), true);
        $states = json_decode(File::get($statesPath), true);
        $combinedCountries = json_decode(File::get($combinedPath), true);

        if (! is_array($countries) || ! is_array($states) || ! is_array($combinedCountries)) {
            return;
        }

        $legacyCountryIdToCode = [];

        foreach ($countries as $index => $country) {
            $countryCode = strtoupper(trim((string) ($country['code'] ?? '')));

            if ($countryCode === '') {
                continue;
            }

            $legacyCountryIdToCode[$index + 1] = $countryCode;
        }

        $combinedByCountryCode = [];

        foreach ($combinedCountries as $country) {
            $countryCode = strtoupper(trim((string) ($country['iso2'] ?? '')));

            if ($countryCode === '') {
                continue;
            }

            $combinedByCountryCode[$countryCode] = is_array($country['states'] ?? null)
                ? $country['states']
                : [];
        }

        $stateRows = DB::table('states')
            ->join('countries', 'countries.id', '=', 'states.country_id')
            ->select('states.id', 'states.code as state_code', 'countries.code as country_code')
            ->get();

        $dbStateIdByKey = [];

        foreach ($stateRows as $row) {
            $countryCode = strtoupper(trim((string) $row->country_code));
            $stateCode = strtoupper(trim((string) $row->state_code));

            if ($countryCode === '' || $stateCode === '') {
                continue;
            }

            $dbStateIdByKey[$countryCode.'|'.$stateCode] = (int) $row->id;
        }

        $citiesToInsert = [];

        foreach ($states as $state) {
            $legacyCountryId = (int) ($state['country_id'] ?? 0);
            $countryCode = $legacyCountryIdToCode[$legacyCountryId] ?? null;
            $stateCode = strtoupper(trim((string) ($state['code'] ?? '')));

            if (! $countryCode || $stateCode === '') {
                continue;
            }

            $stateKey = $countryCode.'|'.$stateCode;
            $dbStateId = $dbStateIdByKey[$stateKey] ?? null;

            if (! $dbStateId) {
                continue;
            }

            $countryStates = $combinedByCountryCode[$countryCode] ?? [];
            $matchedState = null;

            foreach ($countryStates as $countryState) {
                $countryStateCode = strtoupper(trim((string) ($countryState['iso2'] ?? '')));
                $countryStateIso3166 = strtoupper(trim((string) ($countryState['iso3166_2'] ?? '')));

                if ($countryStateCode === $stateCode || str_ends_with($countryStateIso3166, '-'.$stateCode)) {
                    $matchedState = $countryState;

                    break;
                }
            }

            if (! is_array($matchedState) || ! is_array($matchedState['cities'] ?? null)) {
                continue;
            }

            $seenNames = [];

            foreach ($matchedState['cities'] as $city) {
                $cityName = trim((string) ($city['name'] ?? ''));

                if ($cityName === '') {
                    continue;
                }

                $cityNameKey = mb_strtolower($cityName);

                if (isset($seenNames[$cityNameKey])) {
                    continue;
                }

                $seenNames[$cityNameKey] = true;

                $citiesToInsert[] = [
                    'state_id'   => $dbStateId,
                    'name'       => $cityName,
                    'name_ar'    => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
        }

        if ($citiesToInsert === []) {
            return;
        }

        foreach (array_chunk($citiesToInsert, 1000) as $chunk) {
            DB::table('cities')->insertOrIgnore($chunk);
        }
    }
}
