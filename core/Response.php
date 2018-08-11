<?php

namespace Core;

class Response
{
    /**
     * The response data.
     *
     * @var string
     */
    private $data;

    /**
     * The constructor.
     *
     * @param  array    $data
     * @param  int      $code
     * @param  string   $message
     * @return void
     */
    public function __construct (array $data, $code = 200, $message = '')
    {
        self::http($code, $message);

        $this->data = json_encode($data);
    }

    /**
     * Get the response data.
     *
     * @return string
     */
    public function get ()
    {
        return $this->data;
    }

    /**
     * Set a specified response header.
     *
     * @param  int      $code
     * @param  string   $message
     * @return void
     */
    public static function http ($code, $message)
    {
        header("HTTP/1.1 {$code} {$message}");
    }

    /**
     * Suddenly stop the application with an error code and message.
     *
     * @param  int      $code
     * @param  string   $message
     * @return void
     */
    public static function error ($code, $message)
    {
        die(self::http($code, $message));
    }
}
