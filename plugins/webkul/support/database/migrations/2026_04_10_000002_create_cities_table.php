<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('state_id')->constrained('states')->cascadeOnDelete();
            $table->string('name');
            $table->timestamps();

            $table->unique(['state_id', 'name']);
        });

        if (Schema::hasTable('software_cities')) {
            $rows = DB::table('software_cities')
                ->select('state_id', 'name', 'created_at', 'updated_at')
                ->get()
                ->map(fn ($row): array => [
                    'state_id'   => $row->state_id,
                    'name'       => $row->name,
                    'created_at' => $row->created_at,
                    'updated_at' => $row->updated_at,
                ])
                ->all();

            if ($rows !== []) {
                DB::table('cities')->insertOrIgnore($rows);
            }
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('cities');
    }
};
