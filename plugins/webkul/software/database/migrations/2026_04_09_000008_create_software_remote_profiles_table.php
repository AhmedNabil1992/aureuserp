<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('software_remote_profiles', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('license_id')->constrained('software_licenses')->cascadeOnDelete();
            $table->string('anydesk', 50)->nullable();
            $table->string('teamviewer', 50)->nullable();
            $table->string('rustdesk', 50)->nullable();
            $table->text('remark')->nullable();
            $table->timestamps();

            $table->unique('license_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('software_remote_profiles');
    }
};
