<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_license_shift_emails', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('license_id')->constrained('software_licenses')->cascadeOnDelete();
            $table->string('email');
            $table->timestamps();
            $table->unique(['license_id', 'email']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_license_shift_emails');
    }
};
