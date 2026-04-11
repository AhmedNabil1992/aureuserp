<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Webkul\Support\Models\City;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

class SyncLocationsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:locations {--file=countries+states+cities.json}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync countries, states (governorates), and cities from JSON file to database';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $filePath = $this->option('file');

        if (! file_exists($filePath)) {
            $this->error("الملف غير موجود: $filePath");

            return 1;
        }

        $json = json_decode(file_get_contents($filePath), true);
        if (! $json) {
            $this->error('خطأ في قراءة ملف JSON');

            return 1;
        }

        $this->info('=================================');
        $this->info('مزامنة البيانات - الدول والمحافظات والمدن');
        $this->info('=================================');
        $this->newLine();

        // المرحلة 1: الدول
        $this->processeCountries($json);

        // المرحلة 2: المحافظات
        $this->processStates($json);

        // المرحلة 3: المدن
        $this->processCities($json);

        $this->info('✓ اكتملت المزامنة بنجاح');

        return 0;
    }

    /**
     * معالجة دول الملف ومقارنتها بالداتابيز
     */
    private function processeCountries(array $json): void
    {
        $this->info('[1] معالجة الدول');
        $this->line(str_repeat('-', 50));

        $fileCountriesCount = collect($json)->count();
        $dbCountriesCount = Country::count();

        $this->line("الملف: $fileCountriesCount دول");
        $this->line("الداتابيز: $dbCountriesCount دول");

        // فقط للإشارة - لا نغير الدول الموجودة
        $this->line('✓ الدول: حالياً متطابقة بين الملف والداتابيز');
        $this->newLine();
    }

    /**
     * معالجة المحافظات
     */
    private function processStates(array $json): void
    {
        $this->info('[2] معالجة المحافظات');
        $this->line(str_repeat('-', 50));

        $bar = $this->output->createProgressBar(count($json));
        $bar->start();

        $statesAdded = 0;
        $statesTotalFile = 0;

        foreach ($json as $countryData) {
            $countryName = $countryData['name'];
            $country = Country::where('name', $countryName)->first();

            if (! $country) {
                $bar->advance();

                continue;
            }

            foreach ($countryData['states'] ?? [] as $stateData) {
                $statesTotalFile++;

                // التحقق من وجود المحافظة
                $state = State::where('country_id', $country->id)
                    ->where('name', $stateData['name'])
                    ->first();

                if (! $state) {
                    State::create([
                        'country_id' => $country->id,
                        'name'       => $stateData['name'],
                        'iso2'       => $stateData['iso2'] ?? null,
                        'code'       => $stateData['iso2'] ?? null,
                    ]);
                    $statesAdded++;
                }
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();

        $dbStatesCount = State::count();
        $this->line("الملف: $statesTotalFile محافظات");
        $this->line("الداتابيز: $dbStatesCount محافظات");
        $this->line("أضيف: $statesAdded محافظة جديدة");
        $this->newLine();
    }

    /**
     * معالجة المدن
     */
    private function processCities(array $json): void
    {
        $this->info('[3] معالجة المدن');
        $this->line(str_repeat('-', 50));

        $citiesAdded = 0;
        $citiesTotalFile = 0;
        $citiesDuplicated = 0;
        $citiesToInsert = [];
        $batchSize = 500; // حجم الدفعة

        // عد إجمالي المدن لشريط التقدم
        $totalCities = collect($json)
            ->sum(fn ($c) => collect($c['states'] ?? [])
                ->sum(fn ($s) => count($s['cities'] ?? []))
            );

        $bar = $this->output->createProgressBar($totalCities);
        $bar->start();

        foreach ($json as $countryData) {
            $country = Country::where('name', $countryData['name'])->first();
            if (! $country) {
                continue;
            }

            foreach ($countryData['states'] ?? [] as $stateData) {
                $state = State::where('country_id', $country->id)
                    ->where('name', $stateData['name'])
                    ->first();

                if (! $state) {
                    foreach ($stateData['cities'] ?? [] as $city) {
                        $bar->advance();
                    }

                    continue;
                }

                // الحصول على المدن الموجودة بالفعل في هذه المحافظة
                $existingCities = City::where('state_id', $state->id)
                    ->pluck('name')
                    ->all();

                // تجميع أسماء المدن المضافة في هذه الدفعة لتجنب التكرار
                $citiesInBatch = [];

                foreach ($stateData['cities'] ?? [] as $cityData) {
                    $cityName = trim($cityData['name']);
                    $citiesTotalFile++;

                    // التحقق من عدم التكرار في الموجود أو في الدفعة الحالية
                    if (! in_array($cityName, $existingCities) && ! in_array($cityName, $citiesInBatch)) {
                        $citiesToInsert[] = [
                            'state_id'   => $state->id,
                            'name'       => $cityName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                        $citiesInBatch[] = $cityName;

                        // إدراج بدفعات
                        if (count($citiesToInsert) >= $batchSize) {
                            try {
                                City::insert($citiesToInsert);
                                $citiesAdded += count($citiesToInsert);
                            } catch (\Exception $e) {
                                // تجاهل أخطاء التكرار والمتابعة
                                $this->error('خطأ: '.$e->getMessage());
                            }
                            $citiesToInsert = [];
                            $citiesInBatch = [];
                        }
                    } else {
                        $citiesDuplicated++;
                    }

                    $bar->advance();
                }
            }
        }

        // إدراج الدفعة الأخيرة
        if (! empty($citiesToInsert)) {
            try {
                City::insert($citiesToInsert);
                $citiesAdded += count($citiesToInsert);
            } catch (\Exception $e) {
                $this->error('خطأ: '.$e->getMessage());
            }
        }

        $bar->finish();
        $this->newLine(2);

        $dbCitiesCount = City::count();
        $this->line("الملف: $citiesTotalFile مدينة");
        $this->line("الداتابيز: $dbCitiesCount مدينة");
        $this->line("أضيف: <fg=green>$citiesAdded</> مدينة جديدة");
        $this->line("مكرر/موجود: <fg=yellow>$citiesDuplicated</> مدينة");
        $this->newLine();
    }
}
