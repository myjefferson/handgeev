<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Here you may configure your settings for cross-origin resource sharing
    | or "CORS". This determines what cross-origin operations may execute
    | in web browsers. You are free to adjust these settings as needed.
    |
    | To learn more: https://developer.mozilla.org/en-US/docs/Web/HTTP/CORS
    |
    */

    'paths' => ['api/*', 'sanctum/csrf-cookie', 'auth/token', 'health', 'workspaces/*'],

    'allowed_methods' => ['*'],

    'allowed_origins' => [
        'https://handgeev.com',
        'https://www.handgeev.com',
        'https://app.handgeev.com',
        'http://localhost:3000', //removivel
        'http://127.0.0.1:3000', //removivel
        'http://localhost:5174', //removivel
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'],

    'exposed_headers' => [],

    'max_age' => 0,

    'supports_credentials' => false,

];