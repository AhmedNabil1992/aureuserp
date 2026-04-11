<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_licenses', function (Blueprint $table): void {
            $table->string('status', 30)->default('pending')->after('end_date');
            $table->string('request_source', 50)->nullable()->after('status');
            $table->timestamp('requested_at')->nullable()->after('request_source');

            $table->index('status', 'software_licenses_status_idx');
        });
    }

    public function down(): void
    {
        Schema::table('software_licenses', function (Blueprint $table): void {
            $table->dropIndex('software_licenses_status_idx');
            $table->dropColumn(['status', 'request_source', 'requested_at']);
        });
    }
};
