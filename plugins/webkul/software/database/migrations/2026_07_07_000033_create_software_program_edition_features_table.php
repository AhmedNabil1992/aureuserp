<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_program_edition_features', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_edition_id')->constrained('software_program_editions')->cascadeOnDelete();
            $table->foreignId('program_feature_id')->constrained('software_program_features')->cascadeOnDelete();
            $table->decimal('price', 12, 2)->nullable();
            $table->boolean('auto_attach_on_final_license')->default(false);
            $table->boolean('is_complimentary')->default(false);
            $table->unsignedInteger('included_duration_days')->nullable();
            $table->boolean('invoice_on_initial_billing')->default(true);
            $table->boolean('invoice_on_renewal')->default(true);
            $table->boolean('auto_renew_with_license')->default(true);
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();

            $table->unique([
                'program_edition_id',
                'program_feature_id',
            ], 'software_program_edition_feature_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_program_edition_features');
    }
};
