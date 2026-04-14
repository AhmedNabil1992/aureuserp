<?php

return [
    'navigation' => [
        'title' => 'Bills of Materials',
    ],
    'form' => [
        'sections' => [
            'general' => [
                'title'  => 'General',
                'fields' => [
                    'product'         => 'Product',
                    'type'            => 'Type',
                    'quantity'        => 'Quantity',
                    'uom'             => 'Unit of Measure',
                    'reference'       => 'Reference',
                    'company'         => 'Company',
                    'source_location' => 'Source Location',
                    'notes'           => 'Notes',
                ],
            ],
            'components' => [
                'title'  => 'Components',
                'fields' => [
                    'component' => 'Component',
                    'quantity'  => 'Quantity',
                    'uom'       => 'Unit of Measure',
                    'notes'     => 'Notes',
                ],
            ],
        ],
    ],
    'table' => [
        'columns' => [
            'product'         => 'Product',
            'type'            => 'Type',
            'quantity'        => 'Quantity',
            'uom'             => 'Unit of Measure',
            'components'      => 'Components',
            'company'         => 'Company',
            'source_location' => 'Source Location',
            'updated_at'      => 'Updated At',
        ],
    ],
];
