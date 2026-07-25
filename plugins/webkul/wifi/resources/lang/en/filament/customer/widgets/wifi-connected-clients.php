<?php

return [
    'heading'     => 'Currently Connected Clients',
    'description' => 'Live real-time list of active devices and vouchers connected to the network with data consumption and connection start time.',
    'empty'   => [
        'no_clients'      => 'No clients currently connected',
        'no_subscription' => 'No active cloud subscription',
    ],
    'columns' => [
        'username'        => 'Voucher Card',
        'mac_address'     => 'MAC Address',
        'mac_copied'      => 'MAC address copied to clipboard',
        'connected_since' => 'Connected Since',
        'download'        => 'Download',
        'upload'          => 'Upload',
        'total_data'      => 'Total Data',
    ],
    'actions' => [
        'kick'             => 'Disconnect Client',
        'kick_heading'     => 'Disconnect Client from Network',
        'kick_description' => 'Are you sure you want to disconnect this client from the network?',
        'kick_success'     => 'Client disconnect signal sent successfully.',
        'kick_failed'      => 'Failed to send disconnect signal.',
    ],
];
