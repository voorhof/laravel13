<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Application Admin
    |--------------------------------------------------------------------------
    |
    | Values for the initial admin user, used in the database seeder.
    |
    */

    'admin' => [
        'name' => env('APP_ADMIN_NAME', 'John Doe'),
        'email' => env('APP_ADMIN_EMAIL', 'admin@example.com'),
        'password' => env('APP_ADMIN_PASSWORD', 'password'),
    ],
];
