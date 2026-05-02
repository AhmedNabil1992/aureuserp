<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('software_fcm_tokens') && ! Schema::hasTable('fcm_tokens')) {
            Schema::rename('software_fcm_tokens', 'fcm_tokens');

            return;
        }

        if (Schema::hasTable('fcm_tokens')) {
            return;
        }

        Schema::create('fcm_tokens', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->cascadeOnDelete();
            $table->string('token')->unique();
            $table->string('device_name')->nullable();
            $table->timestamps();

            $table->index('user_id');
            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        if (Schema::hasTable('fcm_tokens') && ! Schema::hasTable('software_fcm_tokens')) {
            Schema::rename('fcm_tokens', 'software_fcm_tokens');

            return;
        }

        Schema::dropIfExists('fcm_tokens');
    }
};
