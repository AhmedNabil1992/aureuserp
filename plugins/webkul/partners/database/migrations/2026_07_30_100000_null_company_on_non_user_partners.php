<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        $partners = DB::table('partners_partners')
            ->whereNotNull('company_id')
            ->whereNull('user_id');

        if (Schema::hasColumn('users', 'partner_id')) {
            $partners->whereNotIn(
                'id',
                DB::table('users')->whereNotNull('partner_id')->select('partner_id')
            );
        }

        $partners->update(['company_id' => null]);
    }

    public function down(): void {}
};
