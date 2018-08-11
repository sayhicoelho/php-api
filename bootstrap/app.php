<?php

require __DIR__.'/autoload.php';

require __DIR__.'/../core/functions.php';

$app = new \Core\App;

$app->routeMiddleware([
    'auth' => App\Http\Middleware\AuthenticateMiddleware::class,
    'guest' => App\Http\Middleware\GuestMiddleware::class,
    'role' => App\Http\Middleware\RoleMiddleware::class,
]);

return $app;
