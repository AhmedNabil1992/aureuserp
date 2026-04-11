<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_error_logs', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('device_id')->constrained('software_license_devices')->cascadeOnDelete();
            $table->unsignedInteger('eid')->nullable();
            $table->text('message');
            $table->text('trace');
            $table->string('form_name')->nullable();
            $table->string('image_path')->nullable();
            $table->string('app_version', 50)->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('checked_by')->nullable()->constrained('users')->nullOnDelete();
            $table->dateTime('occurred_at')->nullable();
            $table->timestamps();

            $table->index(['status', 'occurred_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_error_logs');
    }
};
