<?php

return [
    'model_label'   => 'Quick Download',
    'plural_label'  => 'Quick Downloads',
    'fields'        => [
        'title'           => 'File / Tool Name',
        'service_type'    => 'Service / System',
        'version'         => 'Version',
        'file_size'       => 'File Size',
        'upload_file'     => 'Upload File to Server',
        'external_url'    => 'External Direct URL',
        'description'     => 'Description',
        'is_active'       => 'Active for Download',
        'downloads_count' => 'Downloads Count',
        'created_at'      => 'Created At',
    ],
    'helpers'       => [
        'upload_file'  => 'Uploaded to public storage for direct access without login',
        'external_url' => 'Or provide an external direct link (Google Drive, Dropbox, etc.)',
    ],
    'placeholders'  => [
        'all_services' => 'All Services',
    ],
    'actions'       => [
        'download' => 'Download File',
    ],
];
