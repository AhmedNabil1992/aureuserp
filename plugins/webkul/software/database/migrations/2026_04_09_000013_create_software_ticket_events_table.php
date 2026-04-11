<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_ticket_events', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('software_tickets')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('partner_id')->nullable()->constrained('partners_partners')->nullOnDelete();
            $table->string('type', 50)->default('message');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->boolean('is_private')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_ticket_events');
    }
};
