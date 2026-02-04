<?php


return [
    'paths' => ['api/*', 'sanctum/csrf-cookie'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'http://192.168.1.41',
        'http://192.168.1.41:8000',
        'http://192.168.1.41:3000',  // React Native/Flutter dev server
        'http://localhost:3000',      // Local mobile testing
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];