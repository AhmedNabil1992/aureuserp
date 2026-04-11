<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_program_releases', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('program_id')->constrained('software_programs')->cascadeOnDelete();
            $table->string('version_number', 50);
            $table->string('update_link')->nullable();
            $table->date('release_date')->nullable();
            $table->string('file_name')->nullable();
            $table->text('app_terminate')->nullable();
            $table->boolean('is_db_update')->default(false);
            $table->string('db_link')->nullable();
            $table->unsignedInteger('download_times')->default(0);
            $table->boolean('is_active')->default(true);
            $table->string('remark')->nullable();
            $table->timestamps();

            $table->index(['program_id', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_program_releases');
    }
};
