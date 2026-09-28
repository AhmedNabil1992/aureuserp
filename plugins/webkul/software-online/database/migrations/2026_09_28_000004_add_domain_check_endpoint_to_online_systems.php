<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('online_systems', function (Blueprint $table): void {
            $table->string('check_domain_endpoint')
                ->default('/api/tenants/check-domain')
                ->after('api_headers');
        });
    }

    public function down(): void
    {
        Schema::table('online_systems', function (Blueprint $table): void {
            $table->dropColumn('check_domain_endpoint');
        });
    }
};
