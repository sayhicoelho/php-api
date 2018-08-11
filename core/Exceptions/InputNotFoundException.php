<?php

namespace Core\Exceptions;

use Exception;

class InputNotFoundException extends Exception implements ExceptionInterface
{
    /**
     * The constructor.
     *
     * @param  string           $message
     * @param  int              $code
     * @param  \Exception|null  $previous
     * @return void
     */
    public function __construct ($message, $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * Stringify the exception.
     *
     * @return string
     */
    public function __toString ()
    {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }
}
