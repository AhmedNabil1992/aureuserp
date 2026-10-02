<?php

declare(strict_types=1);

namespace Webkul\Account\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use JsonException;
use Throwable;
use Webkul\Account\Enums\JournalType;
use Webkul\Account\Enums\PaymentType;
use Webkul\Account\Facades\Account as AccountFacade;
use Webkul\Account\Models\Journal;
use Webkul\Account\Models\Payment;
use Webkul\Partner\Models\Partner;

class ImportLegacyCustomerBalancesCommand extends Command
{
    protected $signature = 'accounts:import-legacy-customer-balances
        {file : JSON file containing id and Old_Balance fields}
        {--journal=6 : Cash/bank/credit-card journal ID that receives the opening balances}
        {--date= : Accounting date (Y-m-d); defaults to today}
        {--batch=old-system-balances : Stable batch name used to prevent duplicate imports}
        {--commit : Create and post the payments; without this option the command is a dry run}';

    protected $description = 'Import legacy customer balances as posted, unapplied inbound payments.';

    public function handle(): int
    {
        try {
            $rows = $this->readRows((string) $this->argument('file'));
            $journal = $this->resolveJournal();
            $date = $this->resolveDate();
            $batch = $this->resolveBatch();
            $entries = $this->validateRows($rows, $journal, $batch);
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $newEntries = collect($entries)->where('status', 'new');
        $existingEntries = collect($entries)->where('status', 'existing');

        $this->table(
            ['Status', 'Customers', 'Total'],
            [
                ['New', $newEntries->count(), number_format((float) $newEntries->sum('amount'), 2)],
                ['Already imported', $existingEntries->count(), number_format((float) $existingEntries->sum('amount'), 2)],
                ['File total', count($entries), number_format((float) collect($entries)->sum('amount'), 2)],
            ],
        );

        $this->line("Journal: {$journal->id} - {$journal->name}; date: {$date->toDateString()}; batch: {$batch}");

        if (! $this->option('commit')) {
            $this->components->warn('Dry run only. Re-run with --commit after reviewing the totals.');

            return self::SUCCESS;
        }

        if ($newEntries->isEmpty()) {
            $this->components->info('Nothing to import; this batch has already been imported.');

            return self::SUCCESS;
        }

        $created = 0;

        foreach ($newEntries as $entry) {
            try {
                DB::transaction(function () use ($entry, $journal, $date): void {
                    $paymentMethodLine = $journal->inboundPaymentMethodLines()->firstOrFail();

                    $payment = Payment::create([
                        'journal_id'             => $journal->id,
                        'company_id'             => $journal->company_id,
                        'payment_method_line_id' => $paymentMethodLine->id,
                        'currency_id'            => $journal->currency_id ?: $journal->company->currency_id,
                        'partner_id'             => $entry['partner_id'],
                        'payment_type'           => PaymentType::RECEIVE,
                        'payment_reference'      => $entry['reference'],
                        'memo'                   => "Legacy customer balance ({$entry['reference']})",
                        'date'                   => $date,
                        'amount'                 => $entry['amount'],
                    ]);

                    $payment = AccountFacade::postPayment($payment);
                    $payment->generateJournalEntry();
                    $payment->refresh();

                    if (! $payment->move) {
                        throw new \RuntimeException("Payment {$payment->id} did not generate a journal entry.");
                    }

                    AccountFacade::confirmMove($payment->move);
                });

                $created++;
            } catch (Throwable $exception) {
                $this->components->error(
                    "Partner {$entry['partner_id']} failed; its transaction was rolled back: {$exception->getMessage()}"
                );

                return self::FAILURE;
            }
        }

        $this->components->info("Imported and posted {$created} customer balances successfully.");

        return self::SUCCESS;
    }

    /** @return array<int, mixed> */
    private function readRows(string $path): array
    {
        $resolvedPath = $this->resolvePath($path);

        if (! is_file($resolvedPath) || ! is_readable($resolvedPath)) {
            throw new \InvalidArgumentException("JSON file is not readable: {$resolvedPath}");
        }

        try {
            $rows = json_decode((string) file_get_contents($resolvedPath), true, flags: JSON_THROW_ON_ERROR);
        } catch (JsonException $exception) {
            throw new \InvalidArgumentException("Invalid JSON: {$exception->getMessage()}", previous: $exception);
        }

        if (! is_array($rows) || ! array_is_list($rows) || $rows === []) {
            throw new \InvalidArgumentException('The JSON root must be a non-empty array.');
        }

        return $rows;
    }

    private function resolvePath(string $path): string
    {
        if (preg_match('/^(?:[A-Za-z]:[\\\\\/]|[\\\\\/])/', $path) === 1) {
            return $path;
        }

        return base_path($path);
    }

    private function resolveJournal(): Journal
    {
        $journalId = filter_var($this->option('journal'), FILTER_VALIDATE_INT);

        if (! $journalId) {
            throw new \InvalidArgumentException('A valid --journal ID is required.');
        }

        $journal = Journal::query()->with('company')->find($journalId);

        if (! $journal) {
            throw new \InvalidArgumentException("Journal {$journalId} does not exist.");
        }

        if (! in_array($journal->type, [JournalType::CASH, JournalType::BANK, JournalType::CREDIT_CARD], true)) {
            throw new \InvalidArgumentException('The selected journal must be cash, bank, or credit card.');
        }

        if (! $journal->company || ! ($journal->currency_id ?: $journal->company->currency_id)) {
            throw new \InvalidArgumentException('The selected journal has no company currency.');
        }

        if (! $journal->inboundPaymentMethodLines()->exists()) {
            throw new \InvalidArgumentException('The selected journal has no inbound payment method.');
        }

        return $journal;
    }

    private function resolveDate(): CarbonImmutable
    {
        $value = $this->option('date') ?: now()->toDateString();

        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', (string) $value);
        } catch (Throwable) {
            throw new \InvalidArgumentException('The --date value must use Y-m-d format.');
        }

        if (! $date || $date->format('Y-m-d') !== $value) {
            throw new \InvalidArgumentException('The --date value must use Y-m-d format.');
        }

        return $date;
    }

    private function resolveBatch(): string
    {
        $batch = trim((string) $this->option('batch'));

        if ($batch === '' || preg_match('/^[A-Za-z0-9._-]+$/', $batch) !== 1 || strlen($batch) > 100) {
            throw new \InvalidArgumentException('The --batch value must be 1-100 letters, numbers, dots, underscores, or hyphens.');
        }

        return $batch;
    }

    /** @return array<int, array{partner_id: int, amount: float, reference: string, status: string}> */
    private function validateRows(array $rows, Journal $journal, string $batch): array
    {
        $normalized = [];

        foreach ($rows as $index => $row) {
            $line = $index + 1;

            if (! is_array($row) || ! isset($row['id'], $row['Old_Balance'])) {
                throw new \InvalidArgumentException("Row {$line} must contain id and Old_Balance.");
            }

            if (filter_var($row['id'], FILTER_VALIDATE_INT) === false || (int) $row['id'] < 1) {
                throw new \InvalidArgumentException("Row {$line} has an invalid customer id.");
            }

            if (! is_numeric($row['Old_Balance']) || (float) $row['Old_Balance'] <= 0) {
                throw new \InvalidArgumentException("Row {$line} has an invalid Old_Balance.");
            }

            $partnerId = (int) $row['id'];

            if (isset($normalized[$partnerId])) {
                throw new \InvalidArgumentException("Customer {$partnerId} occurs more than once.");
            }

            $amount = round((float) $row['Old_Balance'], 2);

            if (abs($amount - (float) $row['Old_Balance']) > 0.00001) {
                throw new \InvalidArgumentException("Customer {$partnerId} balance has more than two decimal places.");
            }

            $normalized[$partnerId] = $amount;
        }

        $partners = Partner::query()->whereIn('id', array_keys($normalized))->get()->keyBy('id');
        $missing = collect(array_keys($normalized))->diff($partners->keys())->values();

        if ($missing->isNotEmpty()) {
            throw new \InvalidArgumentException('Unknown customer IDs: '.$missing->implode(', '));
        }

        $wrongCompany = $partners->filter(
            fn (Partner $partner): bool => $partner->company_id !== null && $partner->company_id !== $journal->company_id
        )->keys()->values();

        if ($wrongCompany->isNotEmpty()) {
            throw new \InvalidArgumentException('Customers belong to another company: '.$wrongCompany->implode(', '));
        }

        return collect($normalized)->map(function (float $amount, int $partnerId) use ($batch): array {
            $reference = "legacy-balance:{$batch}:{$partnerId}";
            $existing = Payment::query()->where('payment_reference', $reference)->first();

            if ($existing && ($existing->partner_id !== $partnerId || abs((float) $existing->amount - $amount) > 0.00001)) {
                throw new \InvalidArgumentException("Existing reference {$reference} does not match this file.");
            }

            return [
                'partner_id' => $partnerId,
                'amount'     => $amount,
                'reference'  => $reference,
                'status'     => $existing ? 'existing' : 'new',
            ];
        })->values()->all();
    }
}
