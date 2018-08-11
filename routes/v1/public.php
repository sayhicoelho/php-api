<?php

$router->resetMiddlewares();

$router->get('/', function () {
    return response (['message' => trans('message', 'welcome')], 200);
});

$router->get('/example', 'ExampleController@example');
