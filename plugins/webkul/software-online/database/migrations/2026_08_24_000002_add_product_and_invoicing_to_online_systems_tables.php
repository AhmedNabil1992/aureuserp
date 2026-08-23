<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_system_plans', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('system_id')->constrained('products_products')->nullOnDelete();
        });

        Schema::table('online_instances', function (Blueprint $table) {
            $table->foreignId('move_id')->nullable()->after('price')->constrained('accounts_account_moves')->nullOnDelete();
        });

        Schema::table('online_instance_transactions', function (Blueprint $table) {
            $table->foreignId('move_line_id')->nullable()->after('move_id')->constrained('accounts_account_move_lines')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('online_instance_transactions', function (Blueprint $table) {
            $table->dropConstrainedForeignId('move_line_id');
        });

        Schema::table('online_instances', function (Blueprint $table) {
            $table->dropConstrainedForeignId('move_id');
        });

        Schema::table('online_system_plans', function (Blueprint $table) {
            $table->dropConstrainedForeignId('product_id');
        });
    }
};
