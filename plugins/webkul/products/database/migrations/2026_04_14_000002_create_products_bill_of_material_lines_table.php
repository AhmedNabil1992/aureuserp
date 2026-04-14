<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products_bill_of_material_lines', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('bill_of_material_id')->constrained('products_bill_of_materials')->cascadeOnDelete();
            $table->foreignId('component_id')->constrained('products_products')->restrictOnDelete();
            $table->decimal('quantity', 15, 4)->default(1);
            $table->foreignId('uom_id')->nullable()->constrained('unit_of_measures')->nullOnDelete();
            $table->unsignedInteger('sort')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products_bill_of_material_lines');
    }
};
