<?php

declare(strict_types=1);

namespace Webkul\Referral\Services;

use Illuminate\Support\Facades\DB;
use Webkul\Account\Enums\AccountType;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Models\Account;
use Webkul\Account\Models\Journal;
use Webkul\Referral\Models\ReferralAccountingDefault;
use Webkul\Referral\Models\ReferralCampaign;
use Webkul\Support\Models\Company;

class ReferralAccountingDefaults
{
    public const JOURNAL_CODE = 'REFR';

    public const EXPENSE_ACCOUNT_CODE = '962100';

    public const LIABILITY_ACCOUNT_CODE = '213000';

    public function ensureForCompany(int $companyId): ReferralAccountingDefault
    {
        return DB::transaction(function () use ($companyId): ReferralAccountingDefault {
            $company = Company::withoutGlobalScopes()->lockForUpdate()->findOrFail($companyId);
            $existingDefaults = ReferralAccountingDefault::query()->where('company_id', $companyId)->first();
            $expense = $this->account(
                $companyId,
                self::EXPENSE_ACCOUNT_CODE,
                'Referral Discount Expense',
                AccountType::EXPENSE,
                false,
                '962000',
                $existingDefaults?->expense_account_id,
                ['Referral Marketing Expense'],
            );
            $liability = $this->account(
                $companyId,
                self::LIABILITY_ACCOUNT_CODE,
                'Referral Rewards Payable',
                AccountType::LIABILITY_CURRENT,
                true,
                '201000',
                $existingDefaults?->liability_account_id,
            );

            $journal = Journal::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('name', 'Referral Rewards')
                ->first();

            if (! $journal) {
                $journalCode = Journal::withoutGlobalScopes()
                    ->where('company_id', $companyId)
                    ->where('code', self::JOURNAL_CODE)
                    ->exists()
                        ? 'RF'.str_pad((string) $companyId, 2, '0', STR_PAD_LEFT)
                        : self::JOURNAL_CODE;

                $journal = Journal::withoutGlobalScopes()->create([
                    'company_id'         => $companyId,
                    'code'               => $journalCode,
                    'name'               => 'Referral Rewards',
                    'type'               => JournalType::GENERAL,
                    'currency_id'        => $company->currency_id,
                    'auto_check_on_post' => true,
                ]);
            }

            $defaults = ReferralAccountingDefault::query()->updateOrCreate(
                ['company_id' => $companyId],
                ['journal_id' => $journal->id, 'expense_account_id' => $expense->id, 'liability_account_id' => $liability->id],
            );

            ReferralCampaign::query()->where('company_id', $companyId)->update([
                'journal_id'           => $journal->id,
                'expense_account_id'   => $expense->id,
                'liability_account_id' => $liability->id,
            ]);

            return $defaults;
        });
    }

    public function ensureForAllCompanies(): int
    {
        $count = 0;
        Company::withoutGlobalScopes()->pluck('id')->each(function (int $companyId) use (&$count): void {
            $this->ensureForCompany($companyId);
            $count++;
        });

        return $count;
    }

    private function account(
        int $companyId,
        string $code,
        string $name,
        AccountType $type,
        bool $reconcile,
        string $parentCode,
        ?int $existingAccountId = null,
        array $legacyNames = [],
    ): Account {
        $parentId = Account::withoutGlobalScopes()
            ->where('code', $parentCode)
            ->whereHas('companies', fn ($query) => $query->where('companies.id', $companyId))
            ->value('id');

        $account = $existingAccountId
            ? Account::withoutGlobalScopes()
                ->whereKey($existingAccountId)
                ->whereHas('companies', fn ($query) => $query->where('companies.id', $companyId))
                ->first()
            : null;

        $account ??= Account::withoutGlobalScopes()
            ->whereIn('name', [$name, ...$legacyNames])
            ->whereHas('companies', fn ($query) => $query->where('companies.id', $companyId))
            ->first();

        if (! $account) {
            if (Account::withoutGlobalScopes()
                ->where('code', $code)
                ->whereHas('companies', fn ($query) => $query->where('companies.id', $companyId))
                ->exists()) {
                $code .= '-R'.$companyId;
            }

            $account = Account::withoutGlobalScopes()->create([
                'code'       => $code, 'name' => $name, 'account_type' => $type, 'parent_id' => $parentId,
                'deprecated' => false, 'reconcile' => $reconcile, 'non_trade' => true,
            ]);
            $account->companies()->syncWithoutDetaching([$companyId]);
        } else {
            $account->update([
                'name'       => $name, 'account_type' => $type,
                'deprecated' => false, 'reconcile' => $reconcile, 'non_trade' => true,
            ] + ($parentId ? ['parent_id' => $parentId] : []));
        }

        return $account;
    }
}
