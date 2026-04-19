<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles_article_programs', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')
                ->constrained('articles_articles')
                ->cascadeOnDelete();

            $table->foreignId('program_id')
                ->constrained('software_programs')
                ->cascadeOnDelete();

            $table->unique(['article_id', 'program_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles_article_programs');
    }
};
