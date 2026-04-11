<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_license_devices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('license_id')->constrained('software_licenses')->cascadeOnDelete();
            $table->string('computer_id');
            $table->string('license_key')->nullable();
            $table->string('bios_id')->nullable();
            $table->string('disk_id')->nullable();
            $table->string('base_id')->nullable();
            $table->string('video_id')->nullable();
            $table->string('mac_id')->nullable();
            $table->string('device_name')->nullable();
            $table->boolean('is_primary')->default(false);
            $table->timestamps();

            $table->unique(['license_id', 'computer_id']);
            $table->index(['computer_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_license_devices');
    }
};
