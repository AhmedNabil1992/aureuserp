<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_license_subscriptions', function (Blueprint $table): void {
            $table->foreignId('feature_id')
                ->nullable()
                ->after('license_id')
                ->constrained('software_program_features')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('software_license_subscriptions', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('feature_id');
        });
    }
};
