<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_licenses', function (Blueprint $table): void {
            if (! Schema::hasColumn('software_licenses', 'city_id')) {
                $table->foreignId('city_id')
                    ->nullable()
                    ->after('state_id')
                    ->constrained('cities')
                    ->nullOnDelete();
            }
        });
    }

    public function down(): void
    {
        Schema::table('software_licenses', function (Blueprint $table): void {
            if (Schema::hasColumn('software_licenses', 'city_id')) {
                $table->dropConstrainedForeignId('city_id');
            }
        });
    }
};
