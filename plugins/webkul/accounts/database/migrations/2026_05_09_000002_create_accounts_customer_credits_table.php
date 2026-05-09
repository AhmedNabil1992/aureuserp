<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_customer_credits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners_partners')->onDelete('cascade');
            $table->decimal('balance', 15, 2)->default(0)->comment('إجمالي الرصيد الائتماني');
            $table->decimal('reserved_amount', 15, 2)->default(0)->comment('المبلغ المحجوز للطلبات المعلقة');
            $table->string('status')->default('active')->comment('حالة الحساب: active/inactive');
            $table->timestamps();

            $table->unique('partner_id');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_customer_credits');
    }
};
