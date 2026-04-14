<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Webkul\Product\Enums\BomType;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products_bill_of_materials', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products_products')->restrictOnDelete();
            $table->string('type')->default(BomType::Manufacture->value);
            $table->decimal('quantity', 15, 4)->default(1);
            $table->foreignId('uom_id')->nullable()->constrained('unit_of_measures')->nullOnDelete();
            $table->string('reference')->nullable();
            $table->text('notes')->nullable();
            $table->foreignId('company_id')->nullable()->constrained('companies')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products_bill_of_materials');
    }
};
