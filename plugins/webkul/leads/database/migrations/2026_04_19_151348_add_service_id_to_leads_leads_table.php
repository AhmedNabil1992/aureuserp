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
        Schema::table('leads_leads', function (Blueprint $table) {
            $table->foreignId('service_id')->nullable()->after('company_name')->constrained('products_products')->nullOnDelete();
            $table->dropColumn('service_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('leads_leads', function (Blueprint $table) {
            $table->dropForeign(['service_id']);
            $table->dropColumn('service_id');
            $table->string('service_type')->nullable()->after('company_name');
        });
    }
};
