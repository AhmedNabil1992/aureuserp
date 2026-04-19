<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_packages', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('product_id')->constrained('products_products')->cascadeOnDelete();
            $table->text('description')->nullable();
            $table->string('package_type');
            $table->unsignedInteger('quantity');
            $table->decimal('amount', 12, 4)->default(0);
            $table->decimal('dealer_amount', 12, 4)->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_packages');
    }
};
