<?php

return [
    'name' => env('APP_NAME', 'APP'),
    'env' => env('APP_ENV', 'production'),
    'debug' => env('APP_DEBUG', false),
    'timezone' => env('APP_TIMEZONE', 'UTC'),
    'lang' => 'en_US',
    'password_algorithm' => PASSWORD_ARGON2I,
];
