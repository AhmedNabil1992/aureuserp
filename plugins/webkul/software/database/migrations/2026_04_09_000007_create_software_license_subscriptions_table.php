<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_license_subscriptions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('license_id')->constrained('software_licenses')->cascadeOnDelete();
            $table->string('service_type', 50);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->index(['license_id', 'service_type'], 'software_license_sub_ls');
            $table->index(['service_type', 'is_active', 'end_date'], 'software_license_sub');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_license_subscriptions');
    }
};
