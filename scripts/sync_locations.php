<?php

// ملف السكريبت للمقارنة والمزامنة بين الملف والداتابيز

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Webkul\Support\Models\Country;
use Webkul\Support\Models\State;

// قراءة الملف
$json = json_decode(file_get_contents('countries+states+cities.json'), true);

echo '='.str_repeat('=', 60)."\n";
echo "مراقب المزامنة - الدول والمحافظات والمدن\n";
echo '='.str_repeat('=', 60)."\n\n";

echo "[1] المرحلة الأولى: مقارنة الدول\n";
echo str_repeat('-', 60)."\n";

$countries_in_file = collect($json)->pluck('name')->all();
$countries_in_db = Country::pluck('name')->all();

$countries_to_add = array_diff($countries_in_file, $countries_in_db);
$countries_to_remove = array_diff($countries_in_db, $countries_in_file);

echo 'عدد الدول في الملف: '.count($countries_in_file)."\n";
echo 'عدد الدول في الداتابيز: '.count($countries_in_db)."\n";
echo 'دول ناقصة في الداتابيز: '.count($countries_to_add)."\n";
echo 'دول زائدة في الداتابيز: '.count($countries_to_remove)."\n";

if (count($countries_to_add) > 0) {
    echo "\nأول 5 دول ناقصة:\n";
    foreach (array_slice($countries_to_add, 0, 5) as $country) {
        echo "  - $country\n";
    }
}

echo "\n[2] المرحلة الثانية: المحافظات\n";
echo str_repeat('-', 60)."\n";

$total_states_file = 0;
$total_states_db = State::count();

foreach ($json as $country_data) {
    $total_states_file += count($country_data['states'] ?? []);
}

echo "عدد المحافظات في الملف: $total_states_file\n";
echo "عدد المحافظات في الداتابيز: $total_states_db\n";
echo 'المحافظات الناقصة: '.($total_states_file - $total_states_db)."\n";

echo "\n[3] المرحلة الثالثة: المدن\n";
echo str_repeat('-', 60)."\n";

$total_cities_file = 0;
foreach ($json as $country_data) {
    foreach ($country_data['states'] ?? [] as $state_data) {
        $total_cities_file += count($state_data['cities'] ?? []);
    }
}

$total_cities_db = DB::table('cities')->count();

echo "عدد المدن في الملف: $total_cities_file\n";
echo "عدد المدن في الداتابيز: $total_cities_db\n";

echo "\n".str_repeat('=', 60)."\n";
