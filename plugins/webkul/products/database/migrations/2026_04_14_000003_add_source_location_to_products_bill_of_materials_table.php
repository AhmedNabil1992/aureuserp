<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('products_bill_of_materials', function (Blueprint $table): void {
            $table->foreignId('source_location_id')
                ->nullable()
                ->after('company_id')
                ->constrained('inventories_locations')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('products_bill_of_materials', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('source_location_id');
        });
    }
};
