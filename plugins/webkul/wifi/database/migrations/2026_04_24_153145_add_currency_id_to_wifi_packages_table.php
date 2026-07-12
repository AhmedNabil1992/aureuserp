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
        if (! Schema::hasColumn('wifi_packages', 'currency_id')) {
            Schema::table('wifi_packages', function (Blueprint $table): void {
                $table->foreignId('currency_id')->nullable()->after('product_id')->constrained('currencies')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('wifi_packages', 'currency_id')) {
            Schema::table('wifi_packages', function (Blueprint $table): void {
                $table->dropConstrainedForeignId('currency_id');
            });
        }
    }
};
