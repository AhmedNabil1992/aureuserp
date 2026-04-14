<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_program_editions', function (Blueprint $table): void {
            if (! Schema::hasColumn('software_program_editions', 'variant_product_id')) {
                $table->foreignId('variant_product_id')
                    ->nullable()
                    ->after('product_id')
                    ->constrained('products_products')
                    ->nullOnDelete();
            }
        });

        DB::table('software_program_editions as editions')
            ->leftJoin('products_products as direct_product', 'direct_product.id', '=', 'editions.product_id')
            ->leftJoin('software_programs as programs', 'programs.id', '=', 'editions.program_id')
            ->leftJoin('products_products as variants', function ($join): void {
                $join->on('variants.parent_id', '=', 'programs.product_id')
                    ->whereRaw('LOWER(variants.name) LIKE CONCAT("%", LOWER(editions.name), "%")');
            })
            ->whereNull('editions.variant_product_id')
            ->update([
                'editions.variant_product_id' => DB::raw('COALESCE(CASE WHEN direct_product.parent_id IS NOT NULL THEN direct_product.id END, variants.id)'),
            ]);
    }

    public function down(): void
    {
        Schema::table('software_program_editions', function (Blueprint $table): void {
            if (Schema::hasColumn('software_program_editions', 'variant_product_id')) {
                $table->dropConstrainedForeignId('variant_product_id');
            }
        });
    }
};
