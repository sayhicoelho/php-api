<?php

namespace App\Http\Middleware;

use Core\Auth;
use Core\Request;

class GuestMiddleware
{
    /**
     * Handle the middleware.
     *
     * @param  \Core\Request  $request
     * @param  array          $params
     * @return \Core\Response|void
     */
    public function handle(Request $request, ...$params)
    {
        if (Auth::check()) {
            return response(['error' => 'Unauthorized'], 401);
        }
    }
}
