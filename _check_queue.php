<?php

require __DIR__.'/vendor/autoload.php';
$app = require __DIR__.'/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;

echo 'pending jobs: '.DB::table('jobs')->count().PHP_EOL;
echo 'failed jobs: '.DB::table('failed_jobs')->count().PHP_EOL;

$f = DB::table('failed_jobs')->latest('id')->first();
if ($f) {
    echo '--- last failed ('.$f->uuid.') ---'.PHP_EOL;
    echo substr($f->exception, 0, 2000).PHP_EOL;
}
