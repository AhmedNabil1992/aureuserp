<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('accounts_journals', function (Blueprint $table): void {
            $table->foreignId('responsible_user_id')
                ->nullable()
                ->after('bank_account_id')
                ->constrained('users')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('accounts_journals', function (Blueprint $table): void {
            $table->dropConstrainedForeignId('responsible_user_id');
        });
    }
};
