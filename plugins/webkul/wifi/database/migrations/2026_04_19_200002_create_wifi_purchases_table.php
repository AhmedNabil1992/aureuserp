<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_purchases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('wifi_package_id')->constrained('wifi_packages')->cascadeOnDelete();
            $table->foreignId('move_line_id')->unique()->constrained('accounts_account_move_lines')->cascadeOnDelete();
            $table->unsignedBigInteger('cloud_id')->nullable();
            $table->unsignedInteger('quantity');
            $table->unsignedInteger('remaining_quantity')->default(0);
            $table->boolean('is_default')->default(false);
            $table->unsignedBigInteger('creator_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_purchases');
    }
};
