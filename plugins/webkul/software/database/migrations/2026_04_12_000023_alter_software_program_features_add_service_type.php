<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_program_features', function (Blueprint $table): void {
            $table->string('service_type', 50)
                ->nullable()
                ->after('name')
                ->comment('Maps this feature to a subscription service type (e.g. technical_support, mail)');
        });
    }

    public function down(): void
    {
        Schema::table('software_program_features', function (Blueprint $table): void {
            $table->dropColumn('service_type');
        });
    }
};
