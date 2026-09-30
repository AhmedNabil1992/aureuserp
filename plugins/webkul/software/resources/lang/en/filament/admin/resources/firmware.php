<?php

return [
    'navigation' => [
        'label'    => 'IoT firmware',
        'singular' => 'firmware',
    ],
    'form' => [
        'fields' => [
            'device_model'        => 'Device model',
            'device_model_helper' => 'Must exactly match the x-ESP8266-model header.',
            'version'             => 'Version',
            'version_helper'      => 'Use a comparable version such as 1.0.2.',
            'file'                => 'Firmware binary',
            'file_helper'         => 'Upload the compiled .bin file. It is stored privately.',
            'is_active'           => 'Available to devices',
            'notes'               => 'Notes',
        ],
    ],
    'table' => [
        'columns' => [
            'device_model' => 'Device model',
            'version'      => 'Version',
            'file'         => 'File',
            'file_size'    => 'Size',
            'is_active'    => 'Active',
            'updated_at'   => 'Updated at',
        ],
    ],
];
