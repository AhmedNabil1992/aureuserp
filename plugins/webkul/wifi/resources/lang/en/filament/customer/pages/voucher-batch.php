<?php

return [
    'title'   => 'Voucher Batches',
    'empty'   => 'No voucher batch files found',
    'columns' => [
        'nasidentifier'      => 'Router / NAS ID',
        'qty'                => 'Quantity',
        'remaining_vouchers' => 'Remaining (New)',
        'batch_code'         => 'Batch File Name',
        'caption'            => 'Caption',
        'never_expire'       => 'Expiration Date',
        'created_at'         => 'Created At',
    ],
    'actions' => [
        'download'       => 'Download',
        'edit_caption'   => 'Edit Caption',
        'copied_message' => 'Copied to clipboard!',
    ],
    'never_expire_options' => [
        'yes' => 'Never Expire',
    ],
];
