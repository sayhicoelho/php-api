<?php

namespace App\Http\Controllers\Auth;

use App\Session;
use App\Role;
use Core\Auth;
use Core\Request;
use App\Http\Controllers\Controller;

class LoginController extends Controller
{
    /**
     * Handle a login request to the application.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function login (Request $request)
    {
        if ($user = $this->attemptLogin($request))
        {
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

        return response(['error' => Auth::getUsername() . ' or password incorrect.'], 401);
    }

    /**
     * Logout from current session.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function logout (Request $request)
    {
        $token = $request->get('token');

        $session = Session::findBy('token', $token);

        $session->delete();

        return response(['success' => true], 200);
    }

    /**
     * Logout from all sessions.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function logoutAll (Request $request)
    {
        Session::deleteAllFromUser(Auth::id());

        return response (['success' => true], 200);
    }

    /**
     * Check authentication and returns the user.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function checkAuth (Request $request)
    {
        Auth::user()->load(Role::class);

        return response (['user' => Auth::user()->toArray()], 200);
    }

    /**
     * Attempt to authenticate the user.
     *
     * @param  \Core\Request  $request
     * @return \App\User|boolean
     */
    private function attemptLogin (Request $request)
    {
        return Auth::authenticate(
            $request->input(Auth::getUsername()),
            $request->input('password')
        );
    }
}
