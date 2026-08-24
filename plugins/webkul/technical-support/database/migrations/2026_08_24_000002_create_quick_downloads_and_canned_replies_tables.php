<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Quick Downloads / Fast Links (روابط سريعة وبرامج للتحميل العام)
        Schema::create('technical_support_quick_downloads', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('service_type')->nullable()->index(); // wifi, software, accounting, online_system, etc.
            $table->string('file_path')->nullable();
            $table->string('external_url')->nullable();
            $table->string('version')->nullable();
            $table->string('file_size')->nullable();
            $table->unsignedInteger('downloads_count')->default(0);
            $table->boolean('is_active')->default(true);
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            $table->softDeletes();
        });

        // 2. Canned Replies / Quick Responses (ردود سريعة ومختصرات للشات)
        Schema::create('technical_support_canned_replies', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('shortcut')->nullable()->index(); // e.g. /welcome, /thanks
            $table->text('content');
            $table->string('service_type')->nullable()->index();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('technical_support_canned_replies');
        Schema::dropIfExists('technical_support_quick_downloads');
    }
};
