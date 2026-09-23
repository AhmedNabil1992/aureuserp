<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vpn_servers', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('host');
            $table->unsignedSmallInteger('port')->default(443);
            $table->text('admin_password');
            $table->boolean('verify_tls')->default(true);
            $table->unsignedSmallInteger('timeout_seconds')->default(10);
            $table->boolean('is_active')->default(true);
            $table->json('known_hubs')->nullable();
            $table->timestamp('last_connected_at')->nullable();
            $table->timestamp('last_failed_at')->nullable();
            $table->text('last_error')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['host', 'port']);
            $table->index(['is_active', 'name']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vpn_servers');
    }
};
