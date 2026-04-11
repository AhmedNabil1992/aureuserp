<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_license_activities', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('license_id')->constrained('software_licenses')->cascadeOnDelete();
            $table->string('current_version', 50)->nullable();
            $table->dateTime('last_online_at');
            $table->timestamps();

            $table->index(['license_id', 'last_online_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_license_activities');
    }
};
