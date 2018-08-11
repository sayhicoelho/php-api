<?php

namespace Core;

use Core\Exceptions\InvalidConfigFileException;

abstract class Config
{
    /**
     * The application's configurations.
     *
     * @var array
     */
    private static $configs = [];

    /**
     * Load the application's configurations.
     *
     * @param  string  $file
     * @return void
     */
    private static function loadConfigs ($file)
    {
        if (!isset(self::$configs[$file]))
        {
            $config_dir = config_dir("{$file}.php");

            if (file_exists($config_dir))
            {
                self::$configs[$file] = require_once $config_dir;
            }
            else
            {
                throw new InvalidConfigFileException("The config file \"{$file}\" cannot be found.");
            }
        }
    }

    /**
     * Get a specified configuration.
     *
     * @param  string       $file
     * @param  string|null  $key
     * @return string
     */
    public static function get ($file, $key = null)
    {
        self::loadConfigs($file);

        if (!is_null($key))
        {
            return self::$configs[$file][$key] ?? null;
        }

        return self::$configs[$file];
    }

    /**
     * Set a specified configuration.
     *
     * @param  string  $file
     * @param  string  $key
     * @param  mixed   $value
     * @return void
     */
    public static function set ($file, $key, $value)
    {
        self::loadConfigs($file);

        self::$configs[$file][$key] = $value;
    }
}
