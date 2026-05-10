<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('accounts_balance_requests');
        Schema::dropIfExists('accounts_customer_credits');
    }

    public function down(): void {}
};
