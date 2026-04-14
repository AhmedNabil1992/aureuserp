<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_program_features', function (Blueprint $table): void {
            if (! Schema::hasColumn('software_program_features', 'product_id')) {
                $table->foreignId('product_id')
                    ->nullable()
                    ->after('service_type')
                    ->constrained('products_products')
                    ->nullOnDelete();
            }
        });

        DB::table('software_program_features as features')
            ->join('products_products as products', function ($join): void {
                $join->on(DB::raw('LOWER(products.name)'), '=', DB::raw('LOWER(features.name)'))
                    ->where('products.type', '=', 'service');
            })
            ->whereNull('features.product_id')
            ->update([
                'features.product_id' => DB::raw('products.id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('software_program_features', function (Blueprint $table): void {
            if (Schema::hasColumn('software_program_features', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
        });
    }
};
