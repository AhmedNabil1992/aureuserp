<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_voucher_batches', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wifi_purchase_id')->constrained('wifi_purchases')->cascadeOnDelete();
            $table->unsignedBigInteger('cloud_id')->nullable();
            $table->unsignedBigInteger('realm_id')->nullable();
            $table->unsignedBigInteger('dynamic_client_id')->nullable();
            $table->string('nasidentifier')->nullable();
            $table->string('batch_code')->unique();
            $table->unsignedInteger('quantity');
            $table->boolean('never_expire')->default(false);
            $table->string('caption')->nullable();
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_voucher_batches');
    }
};
