<?php

$router->resetMiddlewares();

$router->middleware('auth');

$router->middleware('role', ['admin']);

$router->get('/users/:id', 'UserController@getUser');

$router->get('/users/:id/roles', 'UserController@getRoles');

$router->post('/users/:id/roles', 'UserController@addRole');

$router->put('/users/:id/roles', 'UserController@updateRole');

$router->delete('/users/:id/roles', 'UserController@deleteRole');
