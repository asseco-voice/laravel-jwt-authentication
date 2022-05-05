<?php

namespace Asseco\Auth\App\Exceptions;

use Exception;
use Throwable;

class InvalidTokenException extends Exception
{
    public function __construct($message = 'Invalid token', $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
