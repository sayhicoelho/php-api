<?php

namespace App\Http\Controllers;

use Core\Auth;
use App\Session;
use Core\Request;

class SessionController extends Controller
{
    /**
     * Get all active sessions from user.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function all (Request $request)
    {
        $sessions = Session::findAllFromUser(Auth::id());

        $response = [];

        foreach ($sessions as $session)
        {
            $response['sessions'][] = $session->toArray();
        }

        return response ($response, 200);
    }
}
