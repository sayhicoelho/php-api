<?php

$router->resetMiddlewares();

$router->middleware('auth');

$router->get('/myaccount', function (\Core\Request $request) {
    $user = \Core\Auth::user();
    $name = $user->getField('name');

    return response ([
        'message' => trans('message', 'dashboard_welcome', compact('name'))
    ]);
});
