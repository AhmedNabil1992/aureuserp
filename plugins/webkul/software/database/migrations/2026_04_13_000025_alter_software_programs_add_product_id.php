<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_programs', function (Blueprint $table): void {
            if (! Schema::hasColumn('software_programs', 'product_id')) {
                $table->foreignId('product_id')
                    ->nullable()
                    ->after('slug')
                    ->constrained('products_products')
                    ->nullOnDelete();
            }
        });

        DB::table('software_programs as programs')
            ->join('software_program_editions as editions', 'editions.program_id', '=', 'programs.id')
            ->join('products_products as products', 'products.id', '=', 'editions.product_id')
            ->whereNull('programs.product_id')
            ->whereNull('products.parent_id')
            ->update([
                'programs.product_id' => DB::raw('products.id'),
            ]);
    }

    public function down(): void
    {
        Schema::table('software_programs', function (Blueprint $table): void {
            if (Schema::hasColumn('software_programs', 'product_id')) {
                $table->dropConstrainedForeignId('product_id');
            }
        });
    }
};
