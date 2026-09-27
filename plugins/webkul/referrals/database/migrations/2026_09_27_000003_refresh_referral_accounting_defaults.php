<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Webkul\Referral\Services\ReferralAccountingDefaults;

return new class extends Migration
{
    public function up(): void
    {
        app(ReferralAccountingDefaults::class)->ensureForAllCompanies();
    }

    public function down(): void {}
};
