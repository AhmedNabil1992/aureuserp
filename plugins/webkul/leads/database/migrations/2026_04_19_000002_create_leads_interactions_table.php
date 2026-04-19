<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('leads_interactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lead_id')->constrained('leads_leads')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('type')->default('note');
            $table->string('subject')->nullable();
            $table->text('notes');
            $table->dateTime('interaction_date');
            $table->string('outcome')->nullable();
            $table->string('next_action')->nullable();
            $table->dateTime('follow_up_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('leads_interactions');
    }
};
