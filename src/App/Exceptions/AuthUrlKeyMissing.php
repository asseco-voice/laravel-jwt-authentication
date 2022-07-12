<?php

namespace Asseco\Auth\App\Exceptions;

use Exception;
use Throwable;

class AuthUrlKeyMissing extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Missing configuration: asseco-authentication.auth_url';

        parent::__construct($message, $code, $previous);
    }
}
