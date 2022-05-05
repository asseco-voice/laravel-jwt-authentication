<?php

namespace Asseco\Auth\App\Exceptions;

use Exception;
use Throwable;

class TokenExpirationException extends Exception
{
    public function __construct($message = 'Invalid expiration date', $code = 401, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
