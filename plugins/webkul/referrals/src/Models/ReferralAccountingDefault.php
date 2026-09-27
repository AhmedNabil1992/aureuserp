<?php

declare(strict_types=1);

namespace Webkul\Referral\Models;

use Illuminate\Database\Eloquent\Model;

class ReferralAccountingDefault extends Model
{
    protected $table = 'referral_accounting_defaults';

    protected $fillable = ['company_id', 'journal_id', 'expense_account_id', 'liability_account_id'];
}
