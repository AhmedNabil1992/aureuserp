<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Schema;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class TranslateEgyptLocationsToArabic extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:egypt-arabic {--file=countries+states+cities.json} {--force}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fill Arabic names for Egyptian governorates and cities';

    /**
     * @var array<string, string>
     */
    private array $translationCache = [];

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        if (! Schema::hasColumn('cities', 'name_ar')) {
            $this->error('Column cities.name_ar does not exist. Run migrations first.');

            return self::FAILURE;
        }

        $filePath = (string) $this->option('file');

        if (! file_exists($filePath)) {
            $this->error("File not found: {$filePath}");

            return self::FAILURE;
        }

        $payload = json_decode((string) file_get_contents($filePath), true);

        if (! is_array($payload)) {
            $this->error('Invalid JSON file.');

            return self::FAILURE;
        }

        $egyptSource = collect($payload)->firstWhere('iso2', 'EG');

        if (! is_array($egyptSource)) {
            $this->error('Egypt (EG) was not found in source JSON file.');

            return self::FAILURE;
        }

        $egypt = Country::query()->where('code', 'EG')->first();

        if (! $egypt) {
            $this->error('Egypt country row was not found in database (code=EG).');

            return self::FAILURE;
        }

        $force = (bool) $this->option('force');

        $this->info('Syncing Arabic names for Egypt locations...');
        $governoratesUpdated = $this->syncGovernorates($egypt->id, $egyptSource, $force);
        $citiesUpdated = $this->syncCities($egypt->id, $force);

        $this->newLine();
        $this->info("Governorates updated: {$governoratesUpdated}");
        $this->info("Cities updated: {$citiesUpdated}");
        $this->info('Done.');

        return self::SUCCESS;
    }

    /**
     * @param  array<string, mixed>  $egyptSource
     */
    private function syncGovernorates(int $egyptId, array $egyptSource, bool $force): int
    {
        $nativeByName = collect($egyptSource['states'] ?? [])
            ->filter(fn (array $state): bool => ! empty($state['name']) && ! empty($state['native']))
            ->mapWithKeys(fn (array $state): array => [trim((string) $state['name']) => trim((string) $state['native'])])
            ->all();

        $updated = 0;

        $states = State::query()
            ->where('country_id', $egyptId)
            ->get();

        foreach ($states as $state) {
            if (! $force && filled($state->name_ar)) {
                continue;
            }

            $nameAr = $nativeByName[$state->name] ?? $this->translateToArabic((string) $state->name);

            if (! $nameAr) {
                continue;
            }

            $state->update(['name_ar' => $nameAr]);
            $updated++;
        }

        return $updated;
    }

    private function syncCities(int $egyptId, bool $force): int
    {
        $updated = 0;

        $query = City::query()
            ->whereHas('state', fn ($stateQuery) => $stateQuery->where('country_id', $egyptId));

        if (! $force) {
            $query->where(function ($cityQuery): void {
                $cityQuery->whereNull('name_ar')->orWhere('name_ar', '');
            });
        }

        $total = (clone $query)->count();
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->orderBy('id')->chunkById(200, function ($cities) use (&$updated, $bar): void {
            foreach ($cities as $city) {
                $translation = $this->translateToArabic((string) $city->name);

                if ($translation) {
                    $city->update(['name_ar' => $translation]);
                    $updated++;
                }

                $bar->advance();
            }
        });

        $bar->finish();
        $this->newLine();

        return $updated;
    }

    private function translateToArabic(string $text): ?string
    {
        $cleanText = trim($text);

        if ($cleanText === '') {
            return null;
        }

        if (preg_match('/\\p{Arabic}/u', $cleanText) === 1) {
            return $cleanText;
        }

        if (array_key_exists($cleanText, $this->translationCache)) {
            return $this->translationCache[$cleanText];
        }

        $response = Http::timeout(20)
            ->retry(2, 200)
            ->get('https://translate.googleapis.com/translate_a/single', [
                'client' => 'gtx',
                'sl'     => 'en',
                'tl'     => 'ar',
                'dt'     => 't',
                'q'      => $cleanText,
            ]);

        if (! $response->successful()) {
            return null;
        }

        $payload = $response->json();
        $translated = $payload[0][0][0] ?? null;

        if (! is_string($translated) || trim($translated) === '') {
            return null;
        }

        $this->translationCache[$cleanText] = trim($translated);

        usleep(80000);

        return $this->translationCache[$cleanText];
    }
}
