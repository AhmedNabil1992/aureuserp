<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wifi_voucher_batches', function (Blueprint $table): void {
            if (! Schema::hasColumn('wifi_voucher_batches', 'profile_id')) {
                $table->unsignedBigInteger('profile_id')->nullable()->after('nasidentifier');
            }

            if (! Schema::hasColumn('wifi_voucher_batches', 'days_valid')) {
                $table->unsignedInteger('days_valid')->default(0)->after('profile_id');
            }

            if (! Schema::hasColumn('wifi_voucher_batches', 'hours_valid')) {
                $table->unsignedInteger('hours_valid')->default(0)->after('days_valid');
            }

            if (! Schema::hasColumn('wifi_voucher_batches', 'minutes_valid')) {
                $table->unsignedInteger('minutes_valid')->default(0)->after('hours_valid');
            }

            if (Schema::hasColumn('wifi_voucher_batches', 'dynamic_client_id')) {
                $table->dropColumn('dynamic_client_id');
            }
        });
    }

    public function down(): void
    {
        Schema::table('wifi_voucher_batches', function (Blueprint $table): void {
            if (! Schema::hasColumn('wifi_voucher_batches', 'dynamic_client_id')) {
                $table->unsignedBigInteger('dynamic_client_id')->nullable()->after('realm_id');
            }

            if (Schema::hasColumn('wifi_voucher_batches', 'minutes_valid')) {
                $table->dropColumn('minutes_valid');
            }

            if (Schema::hasColumn('wifi_voucher_batches', 'hours_valid')) {
                $table->dropColumn('hours_valid');
            }

            if (Schema::hasColumn('wifi_voucher_batches', 'days_valid')) {
                $table->dropColumn('days_valid');
            }

            if (Schema::hasColumn('wifi_voucher_batches', 'profile_id')) {
                $table->dropColumn('profile_id');
            }
        });
    }
};
