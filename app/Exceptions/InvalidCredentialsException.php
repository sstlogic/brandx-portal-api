<?php

namespace App\Exceptions;

use Exception;

class InvalidCredentialsException extends Exception
{
    public function __construct(array $credentials)
    {
        $message = "Invalid login credentials for " . json_encode($credentials);
        parent::__construct($message);
    }
}
