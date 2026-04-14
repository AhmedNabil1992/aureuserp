<?php

use Illuminate\Testing\TestResponse;
use Webkul\Inventory\Enums\LocationType;
use Webkul\Inventory\Models\Location;
use Webkul\Inventory\Models\Product as InventoryProduct;
use Webkul\Inventory\Models\ProductQuantity;
use Webkul\Inventory\Models\Warehouse;
use Webkul\Product\Enums\BomType;
use Webkul\Product\Enums\ProductType;
use Webkul\Product\Models\BillOfMaterial;
use Webkul\Product\Models\BillOfMaterialLine;
use Webkul\Sale\Enums\OrderState;
use Webkul\Sale\Models\Order;
use Webkul\Sale\Models\OrderLine;
use Webkul\Security\Enums\PermissionType;
use Webkul\Security\Models\User;
use Webkul\Support\Models\Company;
use Webkul\Support\Models\UOM;

require_once __DIR__.'/../../../../../support/tests/Helpers/SecurityHelper.php';
require_once __DIR__.'/../../../../../support/tests/Helpers/TestBootstrapHelper.php';

const SALES_ORDER_JSON_STRUCTURE = [
    'id',
    'partner_id',
    'payment_term_id',
    'company_id',
    'currency_id',
    'date_order',
    'state',
    'lines',
];

const SALES_ORDER_REQUIRED_FIELDS = [
    'partner_id',
    'payment_term_id',
    'date_order',
    'lines',
];

beforeEach(function () {
    TestBootstrapHelper::ensurePluginInstalled('sales');
    SecurityHelper::disableUserEvents();
});

afterEach(fn () => SecurityHelper::restoreUserEvents());

function actingAsSalesOrderApiUser(array $permissions = []): User
{
    $user = SecurityHelper::authenticateWithPermissions($permissions);

    $user->forceFill([
        'resource_permission' => PermissionType::GLOBAL,
    ])->saveQuietly();

    return $user;
}

function salesOrderRoute(string $action, mixed $order = null): string
{
    $name = "admin.api.v1.sales.orders.{$action}";

    return $order ? route($name, $order) : route($name);
}

function makeLinePayload(array $context, array $overrides = []): array
{
    $line = OrderLine::factory()->make([
        'company_id'       => $context['company_id'],
        'currency_id'      => $context['currency_id'],
        'order_partner_id' => $context['partner_id'],
        'salesman_id'      => $context['user_id'],
    ])->toArray();

    return array_merge([
        'product_id'     => $line['product_id'],
        'product_qty'    => $line['product_qty'],
        'price_unit'     => $line['price_unit'],
        'product_uom_id' => $line['product_uom_id'],
    ], $overrides);
}

function salesOrderPayload(int $lineCount = 2, array $overrides = []): array
{
    $company = Company::factory()->create(['currency_id' => 1]);

    $order = Order::factory()
        ->withPaymentTerms()
        ->make([
            'company_id'  => $company->id,
            'currency_id' => 1,
        ])
        ->toArray();

    $order['date_order'] = $order['date_order']?->format('Y-m-d');

    $order['validity_date'] = $order['validity_date']?->format('Y-m-d');

    $order['lines'] = collect(range(1, $lineCount))
        ->map(fn () => makeLinePayload([
            'company_id'  => $order['company_id'],
            'currency_id' => $order['currency_id'],
            'partner_id'  => $order['partner_id'],
            'user_id'     => $order['user_id'],
        ]))
        ->all();

    return array_replace_recursive($order, $overrides);
}

function createOrderViaApi(array $overrides = []): TestResponse
{
    return test()
        ->postJson(salesOrderRoute('store'), salesOrderPayload(overrides: $overrides))
        ->assertCreated();
}

it('requires authentication to list orders', function () {
    $this->getJson(salesOrderRoute('index'))
        ->assertUnauthorized();
});

it('forbids listing orders without permission', function () {
    actingAsSalesOrderApiUser();

    $this->getJson(salesOrderRoute('index'))
        ->assertForbidden();
});

it('creates an order with lines in a single API call', function () {
    actingAsSalesOrderApiUser(['create_sale_order']);

    $payload = salesOrderPayload(lineCount: 2);
    $response = $this->postJson(salesOrderRoute('store'), $payload);

    $response
        ->assertCreated()
        ->assertJsonPath('message', 'Order created successfully.')
        ->assertJsonPath('data.partner_id', $payload['partner_id'])
        ->assertJsonCount(2, 'data.lines')
        ->assertJsonStructure(['data' => SALES_ORDER_JSON_STRUCTURE]);

    $orderId = $response->json('data.id');

    $this->assertDatabaseHas('sales_orders', [
        'id'              => $orderId,
        'partner_id'      => $payload['partner_id'],
        'payment_term_id' => $payload['payment_term_id'],
    ]);

    $this->assertDatabaseCount('sales_order_lines', 2);
});

it('validates required fields when creating an order', function (string $field) {
    actingAsSalesOrderApiUser(['create_sale_order']);

    $payload = salesOrderPayload();
    unset($payload[$field]);

    $this->postJson(salesOrderRoute('store'), $payload)
        ->assertUnprocessable()
        ->assertJsonValidationErrors([$field]);
})->with(SALES_ORDER_REQUIRED_FIELDS);

it('shows an order for authorized users', function () {
    actingAsSalesOrderApiUser(['view_sale_order']);

    $order = Order::factory()->create();

    $this->getJson(salesOrderRoute('show', $order).'?include=lines')
        ->assertOk()
        ->assertJsonPath('data.id', $order->id)
        ->assertJsonStructure(['data' => SALES_ORDER_JSON_STRUCTURE]);
});

it('returns 404 for a non-existent order', function () {
    actingAsSalesOrderApiUser(['view_sale_order']);

    $this->getJson(salesOrderRoute('show', 999999))
        ->assertNotFound();
});

it('updates an order and syncs its lines via API payload', function () {
    actingAsSalesOrderApiUser(['create_sale_order', 'update_sale_order']);

    $order = Order::factory()->create();

    [$lineToKeepId, $lineToDeleteId] = OrderLine::factory()->count(2)->create([
        'order_id'         => $order->id,
        'company_id'       => $order->company_id,
        'currency_id'      => $order->currency_id,
        'order_partner_id' => $order->partner_id,
        'salesman_id'      => $order->user_id,
        'state'            => $order->state,
    ])->pluck('id')->values()->all();

    $updatePayload = [
        'lines' => [
            [
                'id'          => $lineToKeepId,
                'product_id'  => OrderLine::query()->find($lineToKeepId)->product_id,
                'product_qty' => 5,
                'price_unit'  => 125,
            ],
        ],
    ];

    $this->patchJson(salesOrderRoute('update', $order), $updatePayload)
        ->assertOk()
        ->assertJsonPath('message', 'Order updated successfully.')
        ->assertJsonCount(1, 'data.lines');

    $this->assertDatabaseHas('sales_order_lines', [
        'id'          => $lineToKeepId,
        'product_qty' => 5,
        'price_unit'  => 125,
    ]);

    $this->assertDatabaseMissing('sales_order_lines', ['id' => $lineToDeleteId]);

    expect(OrderLine::query()->where('order_id', $order->id)->count())->toBe(1);
});

it('forbids updating an order without permission', function () {
    actingAsSalesOrderApiUser();

    $order = Order::factory()->create();

    $this->patchJson(salesOrderRoute('update', $order), [])
        ->assertForbidden();
});

it('rejects confirming orders that are not draft or sent', function (OrderState $state) {
    actingAsSalesOrderApiUser(['update_sale_order']);

    $order = Order::factory()->create(['state' => $state]);

    $this->postJson(salesOrderRoute('confirm', $order->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', 'Only draft or sent orders can be confirmed.');
})->with([
    'cancelled order' => [OrderState::CANCEL],
    'sale order'      => [OrderState::SALE],
]);

it('consumes BOM components from stock when confirming a product sale order', function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    $user = actingAsSalesOrderApiUser(['update_sale_order']);

    $warehouse = Warehouse::query()->first() ?? Warehouse::factory()->create();
    $productionLocation = Location::query()->where('type', LocationType::PRODUCTION)->firstOrFail();
    $uom = UOM::query()->firstOrFail();
    $company = Company::query()->findOrFail($warehouse->company_id);

    $component = InventoryProduct::factory()->create([
        'type'       => ProductType::GOODS,
        'company_id' => $company->id,
        'uom_id'     => $uom->id,
        'uom_po_id'  => $uom->id,
        'is_storable'=> true,
        'name'       => 'BOM Component '.str()->random(6),
    ]);

    $finishedProduct = InventoryProduct::factory()->create([
        'type'       => ProductType::PRODUCT,
        'company_id' => $company->id,
        'uom_id'     => $uom->id,
        'uom_po_id'  => $uom->id,
        'is_storable'=> true,
        'name'       => 'Manufactured Product '.str()->random(6),
    ]);

    $billOfMaterial = BillOfMaterial::create([
        'product_id'  => $finishedProduct->id,
        'type'        => BomType::Manufacture,
        'quantity'    => 1,
        'uom_id'      => $uom->id,
        'company_id'  => $company->id,
        'reference'   => 'BOM-TEST-'.str()->upper(str()->random(6)),
        'creator_id'  => $user->getKey(),
    ]);

    BillOfMaterialLine::create([
        'bill_of_material_id' => $billOfMaterial->id,
        'component_id'        => $component->id,
        'quantity'            => 2,
        'uom_id'              => $uom->id,
        'sort'                => 1,
    ]);

    ProductQuantity::create([
        'product_id'    => $component->id,
        'location_id'   => $warehouse->lot_stock_location_id,
        'quantity'      => 10,
        'company_id'    => $company->id,
        'creator_id'    => $user->getKey(),
    ]);

    $order = Order::factory()->withPaymentTerms()->create([
        'company_id'  => $company->id,
        'currency_id' => $company->currency_id,
        'warehouse_id'=> $warehouse->id,
    ]);

    $orderLine = OrderLine::factory()->create([
        'order_id'         => $order->id,
        'company_id'       => $order->company_id,
        'currency_id'      => $order->currency_id,
        'order_partner_id' => $order->partner_id,
        'salesman_id'      => $order->user_id,
        'state'            => $order->state,
        'warehouse_id'     => $warehouse->id,
        'product_id'       => $finishedProduct->id,
        'product_qty'      => 2,
        'product_uom_qty'  => 2,
        'product_uom_id'   => $uom->id,
        'price_unit'       => 100,
        'name'             => 'Manufactured product line',
    ]);

    $this->postJson(salesOrderRoute('confirm', $order->id))
        ->assertOk()
        ->assertJsonPath('data.state', OrderState::SALE->value);

    $this->assertDatabaseHas('inventories_operations', [
        'sale_order_id'           => $order->id,
        'operation_type_id'       => $warehouse->internal_type_id,
        'source_location_id'      => $warehouse->lot_stock_location_id,
        'destination_location_id' => $productionLocation->id,
        'state'                   => 'done',
    ]);

    $this->assertDatabaseHas('inventories_moves', [
        'sale_order_line_id' => $orderLine->id,
        'product_id'         => $component->id,
        'state'              => 'done',
    ]);

    $this->assertDatabaseHas('inventories_product_quantities', [
        'product_id'  => $component->id,
        'location_id' => $warehouse->lot_stock_location_id,
        'quantity'    => 6.0,
    ]);

    $this->assertDatabaseHas('inventories_product_quantities', [
        'product_id'  => $component->id,
        'location_id' => $productionLocation->id,
        'quantity'    => 4.0,
    ]);
});

it('rejects confirming a product sale order when BOM components are short on stock', function () {
    TestBootstrapHelper::ensurePluginInstalled('inventories');

    $user = actingAsSalesOrderApiUser(['update_sale_order']);

    $warehouse = Warehouse::query()->first() ?? Warehouse::factory()->create();
    $uom = UOM::query()->firstOrFail();
    $company = Company::query()->findOrFail($warehouse->company_id);

    $component = InventoryProduct::factory()->create([
        'type'        => ProductType::GOODS,
        'company_id'  => $company->id,
        'uom_id'      => $uom->id,
        'uom_po_id'   => $uom->id,
        'is_storable' => true,
        'name'        => 'Short Component '.str()->random(6),
    ]);

    $finishedProduct = InventoryProduct::factory()->create([
        'type'        => ProductType::PRODUCT,
        'company_id'  => $company->id,
        'uom_id'      => $uom->id,
        'uom_po_id'   => $uom->id,
        'is_storable' => true,
        'name'        => 'Short Manufactured '.str()->random(6),
    ]);

    $billOfMaterial = BillOfMaterial::create([
        'product_id' => $finishedProduct->id,
        'type'       => BomType::Manufacture,
        'quantity'   => 1,
        'uom_id'     => $uom->id,
        'company_id' => $company->id,
        'creator_id' => $user->getKey(),
    ]);

    BillOfMaterialLine::create([
        'bill_of_material_id' => $billOfMaterial->id,
        'component_id'        => $component->id,
        'quantity'            => 3,
        'uom_id'              => $uom->id,
        'sort'                => 1,
    ]);

    ProductQuantity::create([
        'product_id'  => $component->id,
        'location_id' => $warehouse->lot_stock_location_id,
        'quantity'    => 2,
        'company_id'  => $company->id,
        'creator_id'  => $user->getKey(),
    ]);

    $order = Order::factory()->withPaymentTerms()->create([
        'company_id'   => $company->id,
        'currency_id'  => $company->currency_id,
        'warehouse_id' => $warehouse->id,
    ]);

    OrderLine::factory()->create([
        'order_id'         => $order->id,
        'company_id'       => $order->company_id,
        'currency_id'      => $order->currency_id,
        'order_partner_id' => $order->partner_id,
        'salesman_id'      => $order->user_id,
        'state'            => $order->state,
        'warehouse_id'     => $warehouse->id,
        'product_id'       => $finishedProduct->id,
        'product_qty'      => 1,
        'product_uom_qty'  => 1,
        'product_uom_id'   => $uom->id,
        'price_unit'       => 100,
        'name'             => 'Short stock line',
    ]);

    $this->postJson(salesOrderRoute('confirm', $order->id))
        ->assertUnprocessable()
        ->assertJsonPath('message', "Not enough stock to consume BOM components for 'Short stock line'.");

    $this->assertDatabaseHas('sales_orders', [
        'id'    => $order->id,
        'state' => OrderState::DRAFT->value,
    ]);
});

it('soft deletes an order for authorized users', function () {
    actingAsSalesOrderApiUser(['delete_sale_order']);

    $order = Order::factory()->create();

    $this->deleteJson(salesOrderRoute('destroy', $order))
        ->assertOk()
        ->assertJsonPath('message', 'Order deleted successfully.');

    $this->assertSoftDeleted('sales_orders', ['id' => $order->id]);
});

it('forbids deleting an order without permission', function () {
    actingAsSalesOrderApiUser();

    $order = Order::factory()->create();

    $this->deleteJson(salesOrderRoute('destroy', $order))
        ->assertForbidden();
});

it('restores a soft deleted order for authorized users', function () {
    actingAsSalesOrderApiUser(['restore_sale_order']);

    $order = Order::factory()->create();
    $order->delete();

    $this->postJson(salesOrderRoute('restore', $order->id))
        ->assertOk()
        ->assertJsonPath('message', 'Order restored successfully.');

    $this->assertNotSoftDeleted('sales_orders', ['id' => $order->id]);
});

it('force deletes an order for authorized users', function () {
    actingAsSalesOrderApiUser(['force_delete_sale_order']);

    $order = Order::factory()->create();
    $order->delete();

    $this->deleteJson(salesOrderRoute('force-destroy', $order->id))
        ->assertOk()
        ->assertJsonPath('message', 'Order permanently deleted.');

    $this->assertDatabaseMissing('sales_orders', ['id' => $order->id]);
});
