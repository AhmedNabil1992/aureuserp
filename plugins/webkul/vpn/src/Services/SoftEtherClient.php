<?php

declare(strict_types=1);

namespace Webkul\Vpn\Services;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;
use Throwable;
use Webkul\Vpn\Exceptions\SoftEtherApiException;
use Webkul\Vpn\Models\VpnServer;

class SoftEtherClient
{
    public function __construct(private readonly VpnServer $server) {}

    public function serverInfo(): array
    {
        return $this->call('GetServerInfo');
    }

    public function hubs(): array
    {
        return $this->call('EnumHub')['HubList'] ?? [];
    }

    public function users(string $hub): array
    {
        return $this->call('EnumUser', ['HubName_str' => $hub])['UserList'] ?? [];
    }

    public function user(string $hub, string $username): array
    {
        return $this->call('GetUser', [
            'HubName_str' => $hub,
            'Name_str'    => $username,
        ]);
    }

    public function createUser(string $hub, array $attributes): array
    {
        return $this->call('CreateUser', [
            'HubName_str'      => $hub,
            'Name_str'         => $attributes['username'],
            'GroupName_str'    => $attributes['group'] ?? '',
            'Realname_utf'     => $attributes['real_name'] ?? '',
            'Note_utf'         => $attributes['note'] ?? '',
            'ExpireTime_dt'    => $attributes['expires_at'] ?? '',
            'AuthType_u32'     => 1,
            'Auth_Password_str'=> $attributes['password'],
        ]);
    }

    public function deleteUser(string $hub, string $username): array
    {
        return $this->call('DeleteUser', [
            'HubName_str' => $hub,
            'Name_str'    => $username,
        ]);
    }

    public function sessions(string $hub): array
    {
        return $this->call('EnumSession', ['HubName_str' => $hub])['SessionList'] ?? [];
    }

    public function session(string $hub, string $name): array
    {
        return $this->call('GetSessionStatus', [
            'HubName_str' => $hub,
            'Name_str'    => $name,
        ]);
    }

    public function disconnectSession(string $hub, string $name): array
    {
        return $this->call('DeleteSession', [
            'HubName_str' => $hub,
            'Name_str'    => $name,
        ]);
    }

    public function call(string $method, array $parameters = []): array
    {
        try {
            $response = $this->request()->post($this->server->endpoint(), [
                'jsonrpc' => '2.0',
                'id'      => (string) Str::uuid(),
                'method'  => $method,
                'params'  => (object) $parameters,
            ]);

            if (! $response->successful()) {
                throw new SoftEtherApiException("SoftEther returned HTTP {$response->status()}.");
            }

            $payload = $response->json();
            if (! is_array($payload)) {
                throw new SoftEtherApiException('SoftEther returned an invalid JSON response.');
            }

            if (isset($payload['error'])) {
                $message = Arr::get($payload, 'error.message', 'Unknown SoftEther API error');
                $code = Arr::get($payload, 'error.code');

                throw new SoftEtherApiException($code === null ? $message : "{$message} ({$code})");
            }

            $result = $payload['result'] ?? null;
            if (! is_array($result)) {
                throw new SoftEtherApiException('SoftEther response does not contain a valid result.');
            }

            $this->server->forceFill([
                'last_connected_at' => now(),
                'last_error'        => null,
            ])->saveQuietly();

            return $result;
        } catch (SoftEtherApiException $exception) {
            $this->recordFailure($exception);
            throw $exception;
        } catch (ConnectionException $exception) {
            $wrapped = new SoftEtherApiException('Could not connect to the SoftEther server.', previous: $exception);
            $this->recordFailure($wrapped);
            throw $wrapped;
        } catch (Throwable $exception) {
            $wrapped = new SoftEtherApiException('SoftEther request failed: '.$exception->getMessage(), previous: $exception);
            $this->recordFailure($wrapped);
            throw $wrapped;
        }
    }

    private function request(): PendingRequest
    {
        $request = Http::acceptJson()
            ->asJson()
            ->timeout($this->server->timeout_seconds)
            ->connectTimeout(min($this->server->timeout_seconds, 10))
            ->retry(1, 200, throw: false)
            ->withHeaders([
                'X-VPNADMIN-HUBNAME' => '',
                'X-VPNADMIN-PASSWORD'=> $this->server->admin_password,
            ]);

        return $this->server->verify_tls ? $request : $request->withoutVerifying();
    }

    private function recordFailure(Throwable $exception): void
    {
        $this->server->forceFill([
            'last_failed_at' => now(),
            'last_error'     => Str::limit($exception->getMessage(), 1000),
        ])->saveQuietly();
    }
}
