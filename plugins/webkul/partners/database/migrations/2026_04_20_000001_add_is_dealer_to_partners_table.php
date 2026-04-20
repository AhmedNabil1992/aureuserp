<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('partners_partners', function (Blueprint $table): void {
            $table->boolean('is_dealer')->default(false)->after('account_type');
        });
    }

    public function down(): void
    {
        Schema::table('partners_partners', function (Blueprint $table): void {
            $table->dropColumn('is_dealer');
        });
    }
};
