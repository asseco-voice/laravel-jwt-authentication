<?php

namespace Asseco\Auth\App\Exceptions;

use Exception;
use Throwable;

class MissingApiToken extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Credentials array is missing api_token';

        parent::__construct($message, $code, $previous);
    }
}