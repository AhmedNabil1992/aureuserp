<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articles_articles', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('slug')->unique();
            $table->text('summary')->nullable();
            $table->longText('content');
            $table->string('cover_image')->nullable();
            $table->string('video_embed_url')->nullable();
            $table->json('files')->nullable();
            $table->boolean('is_internal')->default(false);
            $table->boolean('is_published')->default(false);
            $table->dateTime('published_at')->nullable();
            $table->integer('sort')->default(0);

            $table->foreignId('category_id')
                ->nullable()
                ->constrained('articles_categories')
                ->nullOnDelete();

            $table->foreignId('author_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('creator_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('last_editor_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->softDeletes();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articles_articles');
    }
};
