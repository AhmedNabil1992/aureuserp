<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles_article_tags', function (Blueprint $table) {
            $table->id();

            $table->foreignId('article_id')
                ->constrained('articles_articles')
                ->cascadeOnDelete();

            $table->foreignId('tag_id')
                ->constrained('articles_tags')
                ->cascadeOnDelete();

            $table->unique(['article_id', 'tag_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles_article_tags');
    }
};
