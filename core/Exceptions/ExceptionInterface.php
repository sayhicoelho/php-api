<?php

namespace Core\Exceptions;

use Exception;

interface ExceptionInterface
{
    public function __construct ($message, $code = 0, Exception $previous = null);

    public function __toString ();
}
