<?php

namespace Core;

class Hash
{
    /**
     * Make a hash using string.
     *
     * @param  string  $password
     * @return string
     */
    public static function make ($password)
    {
        return password_hash($password, config('app', 'password_algorithm'));
    }

    /**
     * Check if hash and password matches.
     *
     * @param  string  $password
     * @param  string  $hash
     * @return boolean
     */
    public static function check ($password, $hash)
    {
        return password_verify($password, $hash);
    }
}
