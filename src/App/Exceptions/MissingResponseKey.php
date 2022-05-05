<?php

namespace Asseco\Auth\App\Exceptions;

use Exception;
use Throwable;

class MissingResponseKey extends Exception
{
    public function __construct(string $responseKey, int $code = 0, ?Throwable $previous = null)
    {
        $message = "Unable to read '$responseKey' from the response!";

        parent::__construct($message, $code, $previous);
    }
}
