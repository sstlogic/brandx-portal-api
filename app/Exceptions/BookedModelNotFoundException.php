<?php

namespace App\Exceptions;

use Exception;

class BookedModelNotFoundException extends Exception
{
    public function __construct(array $response, $path)
    {
        $message = sprintf("Message: %s. Path: %s", $response['message'], $path);
        parent::__construct($message, 400,);
    }
}
