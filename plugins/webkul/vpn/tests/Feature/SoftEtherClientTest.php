<?php

declare(strict_types=1);

namespace Webkul\Vpn\Tests\Feature;

use Illuminate\Config\Repository;
use Illuminate\Container\Container;
use Illuminate\Encryption\Encrypter;
use Illuminate\Http\Client\Factory;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Facade;
use Illuminate\Support\Facades\Http;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\TestCase;
use Webkul\Vpn\Exceptions\SoftEtherApiException;
use Webkul\Vpn\Models\VpnServer;
use Webkul\Vpn\Services\SoftEtherClient;

class SoftEtherClientTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $container = new Container;
        $container->instance('config', new Repository(['app.key' => 'base64:'.base64_encode(random_bytes(32))]));
        $container->instance('encrypter', new Encrypter(random_bytes(32), 'AES-256-CBC'));
        $container->instance(Factory::class, new Factory);
        Container::setInstance($container);
        Facade::setFacadeApplication($container);
    }

    protected function tearDown(): void
    {
        Mockery::close();
        Facade::clearResolvedInstances();
        Facade::setFacadeApplication(null);
        Container::setInstance(null);

        parent::tearDown();
    }

    public function test_it_calls_enum_user_with_softether_admin_headers(): void
    {
        Http::fake(fn (Request $request) => Http::response([
            'jsonrpc' => '2.0',
            'id'      => 'test',
            'result'  => ['HubName_str' => 'MAIN', 'UserList' => [['Name_str' => 'ahmed']]],
        ]));

        $users = (new SoftEtherClient($this->server()))->users('MAIN');

        $this->assertSame('ahmed', $users[0]['Name_str']);
        /** @var Request $request */
        $request = Http::recorded()[0][0];
        $payload = $request->data();

        $this->assertSame('https://vpn.example.com/api/', $request->url());
        $this->assertTrue($request->hasHeader('X-VPNADMIN-PASSWORD', 'secret'));
        $this->assertSame('EnumUser', $payload['method']);
        $this->assertSame('MAIN', $payload['params']->HubName_str);
    }

    public function test_it_sends_password_user_creation_fields(): void
    {
        Http::fake(['*' => Http::response(['jsonrpc' => '2.0', 'result' => ['Name_str' => 'new-user']])]);

        (new SoftEtherClient($this->server()))->createUser('MAIN', [
            'username'  => 'new-user',
            'password'  => 'strong-password',
            'real_name' => 'New User',
        ]);

        Http::assertSent(fn (Request $request): bool => $request['method'] === 'CreateUser'
            && $request['params']->HubName_str === 'MAIN'
            && $request['params']->Name_str === 'new-user'
            && $request['params']->AuthType_u32 === 1
            && $request['params']->Auth_Password_str === 'strong-password');
    }

    public function test_it_converts_json_rpc_errors_to_domain_exceptions(): void
    {
        Http::fake(['*' => Http::response([
            'jsonrpc' => '2.0',
            'error'   => ['code' => 8, 'message' => 'Access denied'],
        ])]);

        $this->expectException(SoftEtherApiException::class);
        $this->expectExceptionMessage('Access denied (8)');

        (new SoftEtherClient($this->server()))->serverInfo();
    }

    public function test_known_hubs_table_state_is_rendered_as_one_string(): void
    {
        $server = new VpnServer;
        $server->setRawAttributes(['known_hubs' => json_encode(['DEFAULT', 'STAFF'])]);

        $this->assertSame('DEFAULT, STAFF', $server->known_hubs_display);
    }

    private function server(): VpnServer
    {
        /** @var VpnServer&MockInterface $server */
        $server = Mockery::mock(VpnServer::class)->makePartial();
        $server->forceFill([
            'name'            => 'Primary',
            'host'            => 'vpn.example.com',
            'port'            => 443,
            'admin_password'  => 'secret',
            'verify_tls'      => true,
            'timeout_seconds' => 10,
        ]);
        $server->shouldReceive('saveQuietly')->andReturnTrue();

        return $server;
    }
}
