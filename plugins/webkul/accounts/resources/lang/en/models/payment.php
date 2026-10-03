<?php

return [
    'title'          => 'Payment',

    'log-attributes' => [
        'date'              => 'Date',
        'payment-type'      => 'Payment Type',
        'partner-type'      => 'Partner Type',
        'memo'              => 'Memo',
        'payment-reference' => 'Payment Reference',
        'amount'            => 'Amount',
        'state'             => 'Status',
        'is-sent'           => 'Sent',
        'is-reconciled'     => 'Reconciled',
        'is-matched'        => 'Matched',
        'partner'           => 'Partner',
        'partner-bank'      => 'Partner Bank',
        'payment-method'    => 'Payment Method',
        'currency'          => 'Currency',
    ],

    'activities' => [
        'confirmed'        => 'The payment was confirmed.',
        'reset-to-draft'   => 'The payment was reset to draft.',
        'canceled'         => 'The payment was canceled.',
        'rejected'         => 'The payment was rejected.',
        'status-changed'   => 'The payment status was changed.',
        'marked-as-sent'   => 'The payment was marked as sent.',
        'marked-as-unsent' => 'The payment was marked as unsent.',
    ],
];
