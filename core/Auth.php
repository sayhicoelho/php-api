<?php

namespace Core;

use PDO;
use App\User;

abstract class Auth
{
    /**
     * The User instance.
     *
     * @var \App\User
     */
    private static $user;

    /**
     * Get the username field.
     *
     * @return string
     */
    public static function getUsername ()
    {
        return config('auth', 'username');
    }

    /**
     * Check if the user is authenticated.
     *
     * @return boolean
     */
    public static function check ()
    {
        return (bool)self::$user;
    }

    /**
     * Check if the user is NOT authenticated.
     *
     * @return boolean
     */
    public static function guest ()
    {
        return !self::check();
    }

    /**
     * Get the user instance.
     *
     * @return \App\User
     */
    public static function user ()
    {
        return self::$user;
    }

    /**
     * Get the user id.
     *
     * @return int
     */
    public static function id ()
    {
        return self::user()->getId();
    }

    /**
     * Attempt to authenticate the user using its username and password.
     *
     * @param  string  $username
     * @param  string  $password
     * @return \App\User|boolean
     */
    public static function authenticate ($username, $password)
    {
        $user = User::findBy(self::getUsername(), $username);

        if (!is_null($user))
        {
            $hash = $user->getField('password');

            if (Hash::check($password, $hash))
            {
                return self::$user = $user;
            }
        }

        return false;
    }

    /**
     * Attempt to authenticate the user using its session token.
     *
     * @param  string  $token
     * @return \App\User|void|null
     */
    public static function authenticateUsingToken ($token)
    {
        return self::$user = User::findByToken($token);
    }
}
