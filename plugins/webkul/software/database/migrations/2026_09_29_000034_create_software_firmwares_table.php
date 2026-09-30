<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_firmwares', function (Blueprint $table): void {
            $table->id();
            $table->string('device_model')->unique();
            $table->string('version', 50);
            $table->string('file_path');
            $table->string('original_file_name');
            $table->unsignedBigInteger('file_size');
            $table->string('md5', 32);
            $table->boolean('is_active')->default(true)->index();
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_firmwares');
    }
};
