<?php

namespace App\Booked\Client;

use App\Exceptions\BookedModelNotFoundException;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Exception;
use Illuminate\Http\Client\Response;
use Illuminate\Validation\ValidationException;

class BookedErrorHandler
{
    /**
     * @throws \Illuminate\Validation\ValidationException|Exception|\App\Exceptions\BookedModelNotFoundException
     */
    public function handle(Response $response, ?string $path = null): mixed
    {
        Bugsnag::notifyException($response->toException());
        
        if ($response->status() === 400 && array_key_exists('errors', $response->json())) {
            $this->handleValidationErrors($response);
        }

        if ($response->status() === 404) {
            $this->handleNotFoundError($response, $path);
        }

        throw new Exception(
            sprintf("Unknown error occurred. Status: %s. Body: %s", $response->status(), $response->body())
        );
    }

    /**
     * @throws \Illuminate\Validation\ValidationException
     */
    private function handleValidationErrors(Response $response)
    {
        throw ValidationException::withMessages($response->json()['errors']);
    }

    /**
     * @throws \App\Exceptions\BookedModelNotFoundException
     */
    private function handleNotFoundError(Response $response, ?string $path = null)
    {
        throw new BookedModelNotFoundException($response->json(), $path);
    }
}
