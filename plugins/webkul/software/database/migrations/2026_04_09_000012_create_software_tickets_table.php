<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_tickets', function (Blueprint $table): void {
            $table->id();
            $table->unsignedInteger('ticket_number')->unique();
            $table->foreignId('program_id')->nullable()->constrained('software_programs')->nullOnDelete();
            $table->foreignId('license_id')->nullable()->constrained('software_licenses')->nullOnDelete();
            $table->foreignId('partner_id')->constrained('partners_partners')->restrictOnDelete();
            $table->foreignId('assigned_to')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('title');
            $table->text('content');
            $table->string('file_path')->nullable();
            $table->string('status', 20)->default('open');
            $table->string('priority', 20)->default('normal');
            $table->boolean('is_unread_admin')->default(true);
            $table->boolean('is_unread_client')->default(false);
            $table->boolean('reopened')->default(false);
            $table->dateTime('first_closed_at')->nullable();
            $table->dateTime('last_closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->softDeletes();
            $table->timestamps();

            $table->index(['partner_id', 'status']);
            $table->index(['assigned_to', 'is_unread_admin']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_tickets');
    }
};
