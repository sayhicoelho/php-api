<?php

if ( ! function_exists('app_dir'))
{
    /**
     * Get the app directory.
     *
     * @param  string  $path
     * @return string
     */
    function app_dir ($path)
    {
        return "../app/{$path}";
    }
}

if ( ! function_exists('config_dir'))
{
    /**
     * Get the configurations directory.
     *
     * @param  string  $path
     * @return string
     */
    function config_dir ($path)
    {
        return "../config/{$path}";
    }
}

if ( ! function_exists('core_dir'))
{
    /**
     * Get the core directory.
     *
     * @param  string  $path
     * @return string
     */
    function core_dir ($path)
    {
        return "../core/{$path}";
    }
}

if ( ! function_exists('exceptions_dir'))
{
    /**
     * Get the exceptions directory.
     *
     * @param  string  $path
     * @return string
     */
    function exceptions_dir ($path)
    {
        return core_dir("exceptions/{$path}");
    }
}

if ( ! function_exists('public_dir'))
{
    /**
     * Get the public directory.
     *
     * @param  string  $path
     * @return string
     */
    function public_dir ($path)
    {
        return "../public/{$path}";
    }
}

if ( ! function_exists('resources_dir'))
{
    /**
     * Get the resources directory.
     *
     * @param  string  $path
     * @return string
     */
    function resources_dir ($path)
    {
        return "../resources/{$path}";
    }
}

if ( ! function_exists('lang_dir'))
{
    /**
     * Get the languages directory.
     *
     * @param  string  $path
     * @return string
     */
    function lang_dir ($path)
    {
        return resources_dir("lang/{$path}");
    }
}

if ( ! function_exists('routes_dir'))
{
    /**
     * Get the routes directory.
     *
     * @param  string  $path
     * @return string
     */
    function routes_dir ($path)
    {
        return "../routes/{$path}";
    }
}

if ( ! function_exists('env'))
{
    /**
     * Get an environment variable.
     *
     * @param  string       $variable
     * @param  string|null  $default
     * @return string
     */
    function env ($variable, $default = null)
    {
        return \Core\Env::get($variable, $default);
    }
}

if ( ! function_exists('config'))
{
    /**
     * Get or set a specified configuration.
     *
     * @param  string        $file
     * @param  string|array  $data
     * @return string|void
     */
    function config ($file, $data)
    {
        if (is_array($data))
        {
            $key = key($data);
            $value = $data[$key];

            \Core\Config::set($file, $key, $value);
        }
        else
        {
            return \Core\Config::get($file, $data);
        }
    }
}

if ( ! function_exists('trans'))
{
    /**
     * Get a specified string based on current locale.
     *
     * @param  string  $file
     * @param  string  $key
     * @param  array   $attributes
     * @return string
     */
    function trans ($file, $key, array $attributes = [])
    {
        return \Core\Localization::get($file, $key, $attributes);
    }
}

if ( ! function_exists('response'))
{
    /**
     * Get the Response instance.
     *
     * @param  array    $data
     * @param  int      $code
     * @param  string   $message
     * @return \Core\Response
     */
    function response (array $data, $code = 200, $message = '')
    {
        return new \Core\Response($data, $code, $message);
    }
}

if ( ! function_exists('str_random'))
{
    /**
     * Generate a random string.
     *
     * @return string
     */
    function str_random ()
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return base64_encode(substr(str_shuffle($characters), 0, mt_rand(50, 62)));
    }
}
