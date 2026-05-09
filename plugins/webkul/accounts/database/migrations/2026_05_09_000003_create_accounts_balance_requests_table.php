<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('accounts_balance_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners_partners')->onDelete('cascade');
            $table->foreignId('payment_transaction_id')->comment('Payment transaction reference');
            $table->decimal('amount', 15, 2)->comment('المبلغ المطلوب');
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending')->comment('حالة الطلب');
            $table->timestamp('requested_at')->useCurrent()->comment('تاريخ إنشاء الطلب');
            $table->timestamp('approved_at')->nullable()->comment('تاريخ الموافقة');
            $table->foreignId('approved_by')->nullable()->constrained('users')->onDelete('set null')->comment('الأدمن الذي وافق');
            $table->text('rejection_reason')->nullable()->comment('سبب الرفض');
            $table->timestamps();

            $table->index('partner_id');
            $table->index('status');
            $table->index('requested_at');
            $table->index(['partner_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('accounts_balance_requests');
    }
};
