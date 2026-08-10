<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('software_program_editions', function (Blueprint $table): void {
            if (! Schema::hasColumn('software_program_editions', 'device_reset_fee')) {
                $table->decimal('device_reset_fee', 12, 2)->default(0.00)->after('license_price');
            }
        });

        Schema::table('software_licenses', function (Blueprint $table): void {
            if (! Schema::hasColumn('software_licenses', 'max_free_device_resets')) {
                $table->unsignedInteger('max_free_device_resets')->default(1)->after('period');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('software_program_editions', function (Blueprint $table): void {
            if (Schema::hasColumn('software_program_editions', 'device_reset_fee')) {
                $table->dropColumn('device_reset_fee');
            }
        });

        Schema::table('software_licenses', function (Blueprint $table): void {
            if (Schema::hasColumn('software_licenses', 'max_free_device_resets')) {
                $table->dropColumn('max_free_device_resets');
            }
        });
    }
};
