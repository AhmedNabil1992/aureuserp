<?php

declare(strict_types=1);

namespace Webkul\SoftwareOnline\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;
use Webkul\SoftwareOnline\Enums\BillingCycle;
use Webkul\SoftwareOnline\Enums\InstanceStatus;
use Webkul\SoftwareOnline\Enums\TransactionType;
use Webkul\SoftwareOnline\Models\OnlineInstance;
use Webkul\SoftwareOnline\Models\OnlineInstanceTransaction;
use Webkul\SoftwareOnline\Services\OnlineBillingService;

class RenewOnlineInstancesCommand extends Command
{
    protected $signature = 'online-systems:renew-due {--hours=24 : Renew instances expiring within this many hours}';

    protected $description = 'Invoice and renew due online instances from customer credit, then synchronize remote tenants.';

    public function handle(OnlineBillingService $billingService): int
    {
        if (! Schema::hasTable('online_instances') || ! Schema::hasTable('online_instance_transactions')) {
            $this->components->info('Software Online is not installed; nothing to renew.');

            return self::SUCCESS;
        }

        $dueBefore = now()->addHours(max(0, (int) $this->option('hours')));
        $renewed = 0;
        $failed = 0;
        $retried = 0;

        OnlineInstanceTransaction::query()
            ->where('type', TransactionType::Renewal->value)
            ->whereIn('remote_sync_status', ['pending', 'failed'])
            ->orderBy('id')
            ->chunkById(100, function ($transactions) use ($billingService, &$retried, &$failed): void {
                foreach ($transactions as $transaction) {
                    try {
                        if ($billingService->syncRenewalTransaction($transaction)) {
                            $retried++;
                        } else {
                            $failed++;
                        }
                    } catch (\Throwable $exception) {
                        $failed++;
                        report($exception);
                    }
                }
            });

        OnlineInstance::query()
            ->where('auto_renew', true)
            ->where('billing_cycle', '!=', BillingCycle::Trial->value)
            ->whereNotIn('status', [
                InstanceStatus::Pending->value,
                InstanceStatus::Provisioning->value,
                InstanceStatus::Failed->value,
                InstanceStatus::Deleting->value,
                InstanceStatus::Deleted->value,
            ])
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', $dueBefore)
            ->orderBy('id')
            ->chunkById(100, function ($instances) use ($billingService, $dueBefore, &$renewed, &$failed): void {
                foreach ($instances as $instance) {
                    try {
                        if ($billingService->renewDueInstance($instance, $dueBefore)) {
                            $renewed++;
                        }
                    } catch (\Throwable $exception) {
                        $failed++;
                        report($exception);
                    }
                }
            });

        $this->info("Renewed: {$renewed}; remote retries: {$retried}; failed: {$failed}.");

        return $failed === 0 ? self::SUCCESS : self::FAILURE;
    }
}
