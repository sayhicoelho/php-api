<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\PasswordResetToken;
use Core\Hash;
use Core\Request;
use App\Http\Controllers\Controller;

class ResetPasswordController extends Controller
{
    /**
     * Send password reset link.
     *
     * @param  \Core\Request  $request
     * @return \Core\Response
     */
    public function sendPasswordResetLink (Request $request)
    {
        $email = $request->input('email');

        $user = User::findBy('email', $email);

        if (!is_null($user))
        {
            $token = str_random();

            PasswordResetToken::deleteAllFromUser($user->getId());

            PasswordResetToken::create([
                'user_id' => $user->getId(),
                'token' => $token,
                'expires_at' => date('Y-m-d H:i:s', strtotime('+30 minutes')),
            ]);

            // TODO: send the link to user email
        }

        return response(['message' => "We sent a token to {$email}."], 200);
    }

    /**
     * Reset the user's password.
     *
     * @param  \Core\Request  $request
     * @param  string  $token
     * @return \Core\Response
     */
    public function reset (Request $request, $token)
    {
        $passwordResetToken = PasswordResetToken::findBy('token', $token);

        if (!is_null($passwordResetToken))
        {
            $user = User::findById($passwordResetToken->getField('user_id'));

            $user->update([
                'password' => Hash::make($request->input('password'))
            ]);

            $passwordResetToken->delete();

            return response (['success' => true], 200);
        }

        return response (['error' => 'Token invalid.'], 401);
    }
}
