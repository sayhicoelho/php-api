<?php

$router->resetMiddlewares();

$router->middleware('guest');

$router->post('/login', 'Auth\\LoginController@login');

$router->post('/register', 'Auth\\RegisterController@register');

$router->post('/password/forgot', 'Auth\\ResetPasswordController@sendPasswordResetLink');

$router->post('/password/reset/:token', 'Auth\\ResetPasswordController@reset');

$router->replaceMiddleware('auth');

$router->get('/sessions', 'SessionController@all');

$router->post('/logout', 'Auth\\LoginController@logout');

$router->post('/logout/all', 'Auth\\LoginController@logoutAll');

$router->get('/auth/check', 'Auth\\LoginController@checkAuth');
