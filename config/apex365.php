<?php
// config/apex365.php
return [
    'microservice' => [
        'file_api_server' => env('FILE_API_SERVER', 'https://cdn.apexdrive365.com/api'),
    ],

    'service' => [
        'telescope_secret_key' => env('TELESCOPE_SECRET_KEY', 'your-secret)'),
    ]
];
