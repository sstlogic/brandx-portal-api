<?php

namespace App\Booked\Client\Responses;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Traits\ForwardsCalls;

/**
 * @mixin \Illuminate\Http\Client\Response
 */
abstract class BookedResponse
{
    use ForwardsCalls;

    public function __construct(
        protected Response $response
    ) {}

    public function __call(string $name, array $arguments)
    {
        return $this->forwardCallTo($this->response, $name, $arguments);
    }
}
