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
        if (Schema::hasTable('time_off_leaves') && Schema::hasTable('calendars')) {
            Schema::table('time_off_leaves', function (Blueprint $table) {
                $table->dropForeign(['calendar_id']);

                $table->foreign('calendar_id')->references('id')->on('calendars')->nullOnDelete();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('time_off_leaves') && Schema::hasTable('employees_calendars')) {
            Schema::table('time_off_leaves', function (Blueprint $table) {
                $table->dropForeign(['calendar_id']);

                $table->foreign('calendar_id')->references('id')->on('employees_calendars')->nullOnDelete();
            });
        }
    }
};
