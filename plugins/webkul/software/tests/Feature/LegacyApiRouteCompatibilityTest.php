<?php

require_once __DIR__.'/../../../support/tests/Helpers/TestBootstrapHelper.php';

use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as RouteFacade;

it('registers every legacy software endpoint with its original HTTP method', function () {
    $expected = [
        'GET'  => [
            'api/user',
            'api/client-id',
            'api/governorates',
            'api/city',
            'api/product',
            'api/check_connection',
            'api/fb-webhook',
        ],
        'POST' => [
            'api/check-for-update',
            'api/insert-logging',
            'api/insert-logging-2',
            'api/check-mail',
            'api/check-valid',
            'api/insert-licenses',
            'api/insert-keys',
            'api/get-key',
            'api/check-key',
            'api/licenses-info',
            'api/send-mail',
            'api/send-generic-mail',
            'api/update-rust-desk',
            'api/get-tech-support-info',
        ],
    ];

    $routes = collect(RouteFacade::getRoutes()->getRoutes());

    foreach ($expected as $method => $uris) {
        foreach ($uris as $uri) {
            $matched = $routes->contains(function (Route $route) use ($method, $uri): bool {
                return $route->uri() === $uri && in_array($method, $route->methods(), true);
            });

            expect($matched)->toBeTrue("Missing legacy route {$method} /{$uri}");
        }
    }
});

it('preserves the legacy Facebook webhook verification response', function () {
    config()->set('services.facebook.webhook_verify_token', 'test-token');

    $this->get('/api/fb-webhook?hub_mode=subscribe&hub_verify_token=test-token&hub_challenge=challenge-123')
        ->assertOk()
        ->assertSeeText('challenge-123');

    $this->get('/api/fb-webhook?hub_mode=subscribe&hub_verify_token=wrong')
        ->assertForbidden()
        ->assertSeeText('Forbidden');
});

it('preserves required legacy request field names', function (string $uri, array $fields) {
    $response = $this->postJson($uri);

    $response->assertUnprocessable()->assertJsonValidationErrors($fields);
})->with([
    'check mail'       => ['/api/check-mail', ['ComputerID']],
    'check valid'      => ['/api/check-valid', ['ComputerID']],
    'check key'        => ['/api/check-key', ['ComputerID']],
    'license details'  => ['/api/licenses-info', ['ComputerID']],
    'support details'  => ['/api/get-tech-support-info', ['ComputerID']],
    'remote id update' => ['/api/update-rust-desk', ['ComputerID', 'RustDeskID']],
]);
