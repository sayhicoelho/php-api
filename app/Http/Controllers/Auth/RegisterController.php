<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Session;
use Core\Auth;
use Core\Hash;
use Core\Request;
use App\Http\Controllers\Controller;

class RegisterController extends Controller
{
    /**
     * Register new user.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function register (Request $request)
    {
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        $session = Session::create([
            'user_id' => $user->getId(),
            'token' => str_random(),
            'user_agent' => $request->userAgent(),
            'ip' => $request->ip(),
            'expires_at' => null,
        ]);

        return response([
            'user' => $user->toArray(),
            'token' => $session->getField('token'),
        ], 200);
    }
}
