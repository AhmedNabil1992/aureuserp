<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_ticket_tag', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('ticket_id')->constrained('software_tickets')->cascadeOnDelete();
            $table->foreignId('tag_id')->constrained('software_tags')->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['ticket_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_ticket_tag');
    }
};
