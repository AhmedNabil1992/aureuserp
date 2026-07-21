<?php

namespace Webkul\Psmonitor\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Throwable;

use InvalidArgumentException;
use Webkul\Software\Models\License;

abstract class RemoteModel extends Model
{
    /**
     * استخدم الموديل بناءً على ترخيص العميل
     */
    public static function forLicense(License $license)
    {
        if (! $license->isRemoteAccessible()) {
            throw new InvalidArgumentException('The provided license cannot be used for remote SQL Server access.');
        }

        return static::onHost($license->server_ip, config('remote_access.remote_database', 'pstm'));
    }
    /**
     * إعداد اتصال SQL Server بناءً على IP (وداتابيز اختيارية)
     */
    protected static function setDynamicConnection(string $host, ?string $database = null): string
    {
        [$parsedHost, $parsedPort] = static::parseHostAndPort($host);
        $baseDatabase = (string) config('database.connections.ps_sqlsrv.database', '');
        $targetDatabase = (string) ($database ?? $baseDatabase);

        $connName = 'ps_sqlsrv_'
            . preg_replace('/[^a-zA-Z0-9_]/', '_', $parsedHost)
            . '_p' . $parsedPort
            . '_db_' . preg_replace('/[^a-zA-Z0-9_]/', '_', $targetDatabase ?: 'default');

        $base = config('database.connections.ps_sqlsrv');

        Config::set("database.connections.$connName", array_merge($base, [
            // Explicitly nullify url so DB_URL env var never overrides the dynamic host.
            'url'      => null,
            'host'     => $parsedHost,
            'port'     => $parsedPort,
            'database' => $targetDatabase,
        ]));

        return $connName;
    }

    /**
     * استخدم الموديل على هوست معين
     */
    public static function onHost(string $host, ?string $database = null)
    {
        $conn = static::setDynamicConnection($host, $database);
        static::primeConnection($conn);

        $instance = new static;
        $instance->setConnection($conn);

        return $instance->newQuery();
    }

    /**
     * Open the SQL connection up-front with a short retry to absorb transient network glitches.
     */
    protected static function primeConnection(string $connectionName): void
    {
        $attempts = max(1, (int) env('PS_DB_CONNECT_RETRIES', 2));
        $sleepMs = max(0, (int) env('PS_DB_RETRY_SLEEP_MS', 250));

        for ($attempt = 1; $attempt <= $attempts; $attempt++) {
            try {
                DB::connection($connectionName)->getPdo();

                return;
            } catch (Throwable $e) {
                if ($attempt < $attempts && static::isTransientConnectionException($e)) {
                    // Rebuild connection only after a transient failure.
                    DB::purge($connectionName);

                    if ($sleepMs > 0) {
                        usleep($sleepMs * 1000);
                    }

                    continue;
                }

                throw $e;
            }
        }
    }

    protected static function isTransientConnectionException(Throwable $e): bool
    {
        $message = strtolower($e->getMessage());

        return str_contains($message, 'sqlstate[08s01]')
            || str_contains($message, 'sqlstate[08001]')
            || str_contains($message, 'sqlstate[hyt00]')
            || str_contains($message, 'tcp provider')
            || str_contains($message, 'timed out')
            || str_contains($message, 'failed to respond')
            || str_contains($message, 'server has failed to respond');
    }

    /**
     * Supports SQL Server host formats:
     * - "192.168.1.10"
     * - "192.168.1.10,1433"
     * - "192.168.1.10:1433"
     * - "SERVER\\INSTANCE" (uses configured default port)
     */
    protected static function parseHostAndPort(string $host): array
    {
        $rawHost = trim($host);
        $defaultPort = (int) config('database.connections.ps_sqlsrv.port', 1433);
        $port = $defaultPort;
        $parsedHost = $rawHost;

        if (str_contains($rawHost, ',')) {
            [$parsedHost, $portStr] = explode(',', $rawHost, 2);
            $port = (int) trim($portStr) ?: $defaultPort;
        } elseif (str_contains($rawHost, ':')) {
            [$parsedHost, $portStr] = explode(':', $rawHost, 2);
            $port = (int) trim($portStr) ?: $defaultPort;
        } elseif (str_contains($rawHost, '\\')) {
            // Named instance: keep server name only and use configured port.
            $parsedHost = explode('\\', $rawHost)[0];
        }

        $parsedHost = trim($parsedHost);

        return [$parsedHost, $port];
    }

    /**
     * Return a query builder that is guaranteed to return zero rows
     * without touching any real database. Used as a safe fallback when
     * the remote SQL Server connection is unavailable.
     */
    public static function emptyQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $connName = 'ps_null_connection';

        if (! Config::has("database.connections.{$connName}")) {
            Config::set("database.connections.{$connName}", [
                'driver'   => 'sqlite',
                'database' => ':memory:',
                'prefix'   => '',
            ]);
        }

        $instance = new static;
        $instance->setConnection($connName);

        return $instance->newQuery()->whereRaw('0 = 1');
    }

    /**
     * Test whether a remote SQL Server host is reachable via a TCP socket check.
     *
     * Parses SQL Server host string formats:
     *   - "192.168.1.1"          → IP only, port from config
     *   - "192.168.1.1,1433"     → IP,Port (sqlsrv comma-separated format)
     *   - "SERVER\INSTANCE"      → named instance, extracts server name, uses port from config
     */
    public static function canConnectToHost(string $host, ?string $database = null): bool
    {
        [$ip, $port] = static::parseHostAndPort($host);
        $timeout = max(1.0, (float) env('PS_DB_LOGIN_TIMEOUT', 30));

        if ($ip === '') {
            return false;
        }

        $socket = @fsockopen($ip, $port, $errno, $errstr, $timeout);

        if ($socket === false) {
            return false;
        }

        fclose($socket);
        return true;
    }
}