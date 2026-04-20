<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_partner_clouds', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('partner_id')->constrained('partners_partners')->cascadeOnDelete();
            $table->unsignedBigInteger('cloud_id');
            $table->timestamps();

            $table->unique(['partner_id', 'cloud_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_partner_clouds');
    }
};
