<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_program_editions', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_id')->constrained('software_programs')->cascadeOnDelete();
            $table->string('name');
            $table->unsignedInteger('max_devices')->default(1);
            $table->decimal('license_cost', 12, 2)->nullable();
            $table->decimal('license_price', 12, 2)->nullable();
            $table->decimal('monthly_renewal', 12, 2)->nullable();
            $table->decimal('annual_renewal', 12, 2)->nullable();
            $table->timestamps();

            $table->unique(['program_id', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_program_editions');
    }
};
