<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Admin Panel Credentials
    |--------------------------------------------------------------------------
    | Set ADMIN_EMAIL and ADMIN_PASSWORD in your .env file.
    | The password is compared against a bcrypt hash generated on first login.
    | There is intentionally only one admin account for simplicity.
    */

    'email'    => env('ADMIN_EMAIL',    'admin@example.com'),
    'password' => env('ADMIN_PASSWORD', 'password'),
];
