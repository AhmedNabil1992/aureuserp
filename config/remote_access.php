<?php

return [
    'remote_database' => env('PS_REMOTE_DATABASE', 'pstm'),

    // Product IDs that represent PS program licenses.
    'ps_product_ids' => array_values(array_filter(array_map(
        static fn (string $id) => (int) trim($id),
        explode(',', (string) env('PS_PRODUCT_IDS', '1'))
    ))),

    // Route name patterns in the customer panel that require an active Remote_Sub.
    'customer_route_names' => [
        'filament.customer.resources.remote-shop-invoices.*',
        'filament.customer.pages.remote*',
        'filament.customer.resources.p-s-devices.*',
        'filament.customer.resources.p-s-device-types.*',
        'filament.customer.resources.p-s-device-prices.*',
        'filament.customer.resources.p-s-item-masters.*',
    ],

    // Path prefixes (without domain) that require an active Remote_Sub.
    'customer_path_prefixes' => [
        'customer/remote',
        'customer/p-s-devices',
        'customer/p-s-device-types',
        'customer/p-s-device-prices',
        'customer/p-s-item-masters',
    ],

    'devices_preferred_columns' => [
        'ID',
        'Device_Name',
        'Device_Type',
        'IP_Address',
        'MAC_Address',
        'Status',
        'Status_IMG',
        'Limit_Time',
        'IsActive',
    ],
];