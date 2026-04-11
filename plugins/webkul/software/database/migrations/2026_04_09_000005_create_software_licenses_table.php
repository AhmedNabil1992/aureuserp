<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_licenses', function (Blueprint $table): void {
            $table->id();
            $table->string('serial_number', 50)->unique();
            $table->foreignId('program_id')->constrained('software_programs')->restrictOnDelete();
            $table->foreignId('edition_id')->constrained('software_program_editions')->restrictOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('state_id')->nullable()->constrained('states')->nullOnDelete();
            $table->string('address')->nullable();
            $table->string('company_name')->nullable();
            $table->string('license_plan', 30);
            $table->unsignedInteger('period')->nullable();
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('server_ip', 45)->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['partner_id', 'is_active']);
            $table->index(['program_id', 'edition_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_licenses');
    }
};
