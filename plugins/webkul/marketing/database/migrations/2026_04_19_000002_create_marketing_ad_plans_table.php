<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('marketing_ad_plans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('campaign_id')->constrained('marketing_campaigns')->cascadeOnDelete();

            // Planned values (set at start of month)
            $table->decimal('planned_budget', 12, 2)->default(0);
            $table->integer('planned_reach')->default(0);
            $table->integer('planned_messages')->default(0);
            $table->integer('planned_conversions')->default(0);

            // Actual values (filled at end of month)
            $table->decimal('actual_budget', 12, 2)->nullable();
            $table->integer('actual_reach')->nullable();
            $table->integer('actual_messages')->nullable();
            $table->integer('actual_conversions')->nullable();
            $table->integer('actual_leads')->nullable();

            $table->text('notes')->nullable();
            $table->foreignId('creator_id')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('marketing_ad_plans');
    }
};
