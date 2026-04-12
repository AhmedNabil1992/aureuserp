<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('software_license_invoices', function (Blueprint $table): void {
            $table->foreignId('account_move_id')
                ->nullable()
                ->after('notes')
                ->constrained('accounts_account_moves')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('software_license_invoices', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('account_move_id');
        });
    }
};
