<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_license_invoices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('license_id')->constrained('software_licenses')->cascadeOnDelete();
            $table->foreignId('program_id')->nullable()->constrained('software_programs')->nullOnDelete();
            $table->foreignId('edition_id')->nullable()->constrained('software_program_editions')->nullOnDelete();
            $table->string('license_plan', 30);
            $table->string('invoice_number', 50)->unique();
            $table->string('item_name');
            $table->decimal('quantity', 8, 2)->default(1);
            $table->decimal('unit_price', 12, 2)->default(0);
            $table->decimal('amount', 12, 2)->default(0);
            $table->foreignId('billed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('billed_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['license_id', 'license_plan'], 'software_license_invoice_ls');
            $table->index('invoice_number', 'software_license_invoice_num');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_license_invoices');
    }
};
