<?php

namespace Core;

use Core\Exceptions\InputNotFoundException;

class Request
{
    /**
     * The request inputs.
     *
     * @var array
     */
    private $inputs = [];

    /**
     * The request files.
     *
     * @var array
     */
    private $files = [];

    /**
     * The inputs and files together.
     *
     * @var array
     */
    private $all = [];

    /**
     * The constructor.
     *
     * @return void
     */
    public function __construct ()
    {
        $this->setInputs();

        $this->setFiles();

        $this->mergeAll();
    }

    /**
     * Set the request inputs.
     *
     * @return void
     */
    private function setInputs ()
    {
        if (isset($_SERVER['CONTENT_TYPE']) && $_SERVER['CONTENT_TYPE'] === 'application/json' || $_SERVER['REQUEST_METHOD'] !== 'POST')
        {
            $this->inputs = json_decode(file_get_contents('php://input'), true);
        }
        else
        {
            $this->inputs = $_POST;
        }
    }

    /**
     * Set the request files.
     *
     * @return void
     */
    private function setFiles ()
    {
        $this->files = $_FILES;
    }

    /**
     * Merge the request inputs and files.
     *
     * @return void
     */
    private function mergeAll ()
    {
        $this->all = @array_merge($this->inputs, $this->files);
    }

    /**
     * Get a specified input from request.
     *
     * @param  string  $name
     * @return string|null
     */
    public function input ($name)
    {
        return $this->inputs[$name] ?? null;
    }

    /**
     * Get a specified parameter value from URL.
     *
     * @param  string  $key
     * @return string
     */
    public function get ($key)
    {
        return $_GET[$key] ?? null;
    }

    /**
     * Get a specified file from request.
     *
     * @param  string  $name
     * @return \Core\File
     */
    public function file ($name)
    {
        return new File($this->files[$name]);
    }

    /**
     * Get all the inputs.
     *
     * @return array
     */
    public function all ()
    {
        return $this->all;
    }

    /**
     * Replace a specified request input.
     *
     * @param  string  $key
     * @param  string  $value
     * @return void
     */
    public function replace ($key, $value)
    {
        if (isset($this->inputs[$name]))
        {
            $this->inputs[$name] = $value;
        }
        else
        {
            throw new InputNotFoundException("Cannot replace input \"{$key}\" because it does not exist in Request.");
        }
    }

    /**
     * Get a specified request header.
     *
     * @param  string  $key
     * @return string
     */
    public function header ($key)
    {
        return $_SERVER[$key] ?? null;
    }

    /**
     * Get the request User Agent.
     *
     * @return string
     */
    public function userAgent ()
    {
        return $this->header('HTTP_USER_AGENT');
    }

    /**
     * Get the request IP Address.
     *
     * @return string
     */
    public function ip ()
    {
        if (!is_null($this->header('HTTP_CLIENT_IP')))
        {
            return $this->header('HTTP_CLIENT_IP');
        }
        else if (!is_null($this->header('HTTP_X_FORWARDED_FOR')))
        {
            return $this->header('HTTP_X_FORWARDED_FOR');
        }

        return $this->header('REMOTE_ADDR');
    }
}
