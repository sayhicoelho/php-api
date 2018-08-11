<?php

namespace Core;

use Core\Exceptions\EnvMissingException;
use Core\Exceptions\InvalidEnvSetupException;

abstract class Env
{
    /**
     * The application's environment variables.
     *
     * @var array
     */
    private static $variables = [];

    /**
     * Initialize the environment variables.
     *
     * @return void
     */
    public static function init ()
    {
        $env_path = __DIR__.'/../.env';
        $env_example_path = __DIR__.'/../.env.example';

        if (file_exists($env_path))
        {
            $env = parse_ini_file($env_path);
            $env_example = parse_ini_file($env_example_path);

            $diffKeys = array_diff_key($env_example, $env);

            if (!count($diffKeys))
            {
                self::set($env);
            }
            else
            {
                $diffKeysAsString = implode(', ', array_keys($diffKeys));

                throw new InvalidEnvSetupException("Invalid environment setup. Please make sure you have added {$diffKeysAsString}.");
            }
        }
        else
        {
            throw new EnvMissingException('The environment file is missing. Please copy .env.example and paste to .env.');
        }
    }

    /**
     * Set an environment variable.
     *
     * @param  array  $env
     * @return void
     */
    private static function set (array $env)
    {
        self::$variables = $env;
    }

    /**
     * Get an environment variable.
     *
     * @param  string       $name
     * @param  string|null  $default
     * @return string
     */
    public static function get ($name, $default = null)
    {
        return self::$variables[$name] ?? $default;
    }
}
