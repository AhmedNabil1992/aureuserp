<?php

declare(strict_types=1);

namespace Webkul\Wifi\Console\Commands;

use Carbon\CarbonImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use JsonException;
use Throwable;
use Webkul\Partner\Models\Partner;
use Webkul\Wifi\Models\Cloud;
use Webkul\Wifi\Models\WifiPackage;
use Webkul\Wifi\Models\WifiPartnerCloud;
use Webkul\Wifi\Models\WifiPurchase;

class ImportLegacyWifiPurchasesCommand extends Command
{
    protected $signature = 'wifi:import-legacy-purchases
        {file : JSON file containing the legacy Wi-Fi purchases}
        {--commit : Import the purchases; without this option the command is a dry run}';

    protected $description = 'Import legacy Wi-Fi purchases without creating accounting invoices.';

    public function handle(): int
    {
        if (! Schema::hasColumn('wifi_purchases', 'legacy_purchase_id')) {
            $this->components->error('Run the database migrations before importing legacy Wi-Fi purchases.');

            return self::FAILURE;
        }

        try {
            $entries = $this->validateRows($this->readRows((string) $this->argument('file')));
        } catch (Throwable $exception) {
            $this->components->error($exception->getMessage());

            return self::FAILURE;
        }

        $newEntries = collect($entries)->where('status', 'new');
        $existingEntries = collect($entries)->where('status', 'existing');

        $this->table(
            ['Status', 'Purchases', 'Quantity', 'Consumed', 'Remaining'],
            [
                $this->summarize('New', $newEntries),
                $this->summarize('Already imported', $existingEntries),
                $this->summarize('File total', collect($entries)),
            ],
        );

        if (! $this->option('commit')) {
            $this->components->warn('Dry run only. Re-run with --commit after reviewing the totals.');

            return self::SUCCESS;
        }

        if ($newEntries->isEmpty()) {
            $this->components->info('Nothing to import; all legacy purchases already exist.');

            return self::SUCCESS;
        }

        try {
            DB::transaction(function () use ($newEntries): void {
                foreach ($newEntries as $entry) {
                    $purchase = new WifiPurchase([
                        'legacy_purchase_id'       => $entry['legacy_purchase_id'],
                        'partner_id'               => $entry['partner_id'],
                        'wifi_package_id'          => $entry['wifi_package_id'],
                        'cloud_id'                 => $entry['cloud_id'],
                        'quantity'                 => $entry['quantity'],
                        'remaining_quantity'       => $entry['remaining_quantity'],
                        'legacy_consumed_quantity' => $entry['consumed_quantity'],
                        'is_default'               => false,
                    ]);

                    $purchase->created_at = $entry['purchased_at']->startOfDay();
                    $purchase->updated_at = now();
                    $purchase->save();
                }
            });
        } catch (Throwable $exception) {
            $this->components->error('Import rolled back: '.$exception->getMessage());

            return self::FAILURE;
        }

        $this->components->info("Imported {$newEntries->count()} legacy Wi-Fi purchases successfully.");

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

    /**
     * @return array<int, array{
     *     legacy_purchase_id: int,
     *     partner_id: int,
     *     wifi_package_id: int,
     *     cloud_id: int,
     *     quantity: int,
     *     remaining_quantity: int,
     *     consumed_quantity: int,
     *     purchased_at: CarbonImmutable,
     *     status: string
     * }>
     */
    private function validateRows(array $rows): array
    {
        $entries = [];

        foreach ($rows as $index => $row) {
            $line = $index + 1;
            $required = [
                'legacy_purchase_id',
                'partner_id',
                'wifi_package_id',
                'cloud_id',
                'quantity',
                'remaining_quantity',
                'purchased_at',
            ];

            if (! is_array($row) || array_diff($required, array_keys($row)) !== []) {
                throw new \InvalidArgumentException("Row {$line} is missing one or more required fields.");
            }

            foreach (['legacy_purchase_id', 'partner_id', 'wifi_package_id', 'cloud_id', 'quantity'] as $field) {
                if (filter_var($row[$field], FILTER_VALIDATE_INT) === false || (int) $row[$field] < 1) {
                    throw new \InvalidArgumentException("Row {$line} has an invalid {$field}.");
                }
            }

            if (filter_var($row['remaining_quantity'], FILTER_VALIDATE_INT) === false || (int) $row['remaining_quantity'] < 0) {
                throw new \InvalidArgumentException("Row {$line} has an invalid remaining_quantity.");
            }

            $legacyPurchaseId = (int) $row['legacy_purchase_id'];
            $quantity = (int) $row['quantity'];
            $remainingQuantity = (int) $row['remaining_quantity'];

            if ($remainingQuantity > $quantity) {
                throw new \InvalidArgumentException("Row {$line} remaining_quantity exceeds quantity.");
            }

            if (isset($entries[$legacyPurchaseId])) {
                throw new \InvalidArgumentException("Legacy purchase {$legacyPurchaseId} occurs more than once.");
            }

            $purchasedAt = $this->parseDate((string) $row['purchased_at'], $line);

            $entries[$legacyPurchaseId] = [
                'legacy_purchase_id' => $legacyPurchaseId,
                'partner_id'         => (int) $row['partner_id'],
                'wifi_package_id'    => (int) $row['wifi_package_id'],
                'cloud_id'           => (int) $row['cloud_id'],
                'quantity'           => $quantity,
                'remaining_quantity' => $remainingQuantity,
                'consumed_quantity'  => $quantity - $remainingQuantity,
                'purchased_at'       => $purchasedAt,
            ];
        }

        $entries = collect($entries);
        $this->assertIdsExist(Partner::query(), $entries->pluck('partner_id')->unique()->all(), 'customer');
        $this->assertIdsExist(WifiPackage::query(), $entries->pluck('wifi_package_id')->unique()->all(), 'Wi-Fi package');
        $this->assertIdsExist(Cloud::query(), $entries->pluck('cloud_id')->unique()->all(), 'cloud');

        foreach ($entries as $entry) {
            $mappingExists = WifiPartnerCloud::query()
                ->where('partner_id', $entry['partner_id'])
                ->where('cloud_id', $entry['cloud_id'])
                ->exists();

            if (! $mappingExists) {
                throw new \InvalidArgumentException(
                    "Customer {$entry['partner_id']} is not assigned to cloud {$entry['cloud_id']}."
                );
            }
        }

        return $entries->map(function (array $entry): array {
            $existing = WifiPurchase::query()
                ->where('legacy_purchase_id', $entry['legacy_purchase_id'])
                ->first();

            if ($existing && ! $this->matchesExistingPurchase($existing, $entry)) {
                throw new \InvalidArgumentException(
                    "Legacy purchase {$entry['legacy_purchase_id']} already exists with different data."
                );
            }

            return $entry + ['status' => $existing ? 'existing' : 'new'];
        })->values()->all();
    }

    private function parseDate(string $value, int $line): CarbonImmutable
    {
        try {
            $date = CarbonImmutable::createFromFormat('!Y-m-d', $value);
        } catch (Throwable) {
            throw new \InvalidArgumentException("Row {$line} purchased_at must use Y-m-d format.");
        }

        if (! $date || $date->format('Y-m-d') !== $value) {
            throw new \InvalidArgumentException("Row {$line} purchased_at must use Y-m-d format.");
        }

        return $date;
    }

    private function assertIdsExist($query, array $ids, string $label): void
    {
        $missing = collect($ids)->diff($query->whereIn('id', $ids)->pluck('id'))->values();

        if ($missing->isNotEmpty()) {
            throw new \InvalidArgumentException("Unknown {$label} IDs: {$missing->implode(', ')}");
        }
    }

    private function matchesExistingPurchase(WifiPurchase $purchase, array $entry): bool
    {
        return (int) $purchase->partner_id === $entry['partner_id']
            && (int) $purchase->wifi_package_id === $entry['wifi_package_id']
            && (int) $purchase->cloud_id === $entry['cloud_id']
            && (int) $purchase->quantity === $entry['quantity']
            && (int) $purchase->legacy_consumed_quantity === $entry['consumed_quantity'];
    }

    /** @return array<int, int|string> */
    private function summarize(string $label, $entries): array
    {
        return [
            $label,
            $entries->count(),
            (int) $entries->sum('quantity'),
            (int) $entries->sum('consumed_quantity'),
            (int) $entries->sum('remaining_quantity'),
        ];
    }
}
