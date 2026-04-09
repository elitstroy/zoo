<?php

return [
    // API endpoints
    'api_url' => 'https://monitoring.gosstroyportal.by',

    // Credentials
    'login' => env('GOSSTROY_LOGIN'),
    'password' => env('GOSSTROY_PASSWORD'),

    // Paths
    'download_dir' => storage_path('app/gosstroy/downloads'),

    // File naming
    'template_filename' => 'template.xlsx',
    'actual_list_filename' => 'actual_list.xlsx',
];
