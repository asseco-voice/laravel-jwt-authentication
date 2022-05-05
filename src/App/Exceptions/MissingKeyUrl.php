<?php

namespace Asseco\Auth\App\Exceptions;

use Exception;
use Throwable;

class MissingKeyUrl extends Exception
{
    public function __construct(int $code = 0, ?Throwable $previous = null)
    {
        $message = 'Missing configuration: asseco-authentication.iam_key_url';

        parent::__construct($message, $code, $previous);
    }
}