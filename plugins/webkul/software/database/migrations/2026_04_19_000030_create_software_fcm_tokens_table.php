<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_fcm_tokens', function (Blueprint $table): void {
            $table->id();

            // token owner — either an admin user or a customer (partner)
            $table->foreignId('user_id')->nullable()->constrained('users')->cascadeOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->cascadeOnDelete();

            // the FCM registration token sent from the Flutter / web client
            $table->string('token')->unique();

            // device label for multi-device support (optional but useful)
            $table->string('device_name')->nullable();

            $table->timestamps();

            $table->index('user_id');
            $table->index('partner_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_fcm_tokens');
    }
};
