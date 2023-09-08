<?php

namespace App\Booked\Client;

use App\Booked\Client\Endpoints\AttributeEndpoints;
use App\Booked\Client\Endpoints\ReservationEndpoints;
use App\Booked\Client\Endpoints\SlotEndpoints;
use App\Booked\Client\Endpoints\UserEndpoints;
use App\Exceptions\InvalidCredentialsException;
use Http;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Str;

class BookedClient
{
    use BookedEndpoints;
    use BookedAuthentication;
    use SlotEndpoints;
    use ReservationEndpoints;
    use UserEndpoints;
    use AttributeEndpoints;

    public function __construct(
        private BookedClientConfig $config,
        private BookedErrorHandler $errorHandler
    ) {
    }

    public function getInstance(): static
    {
        return $this;
    }

    /**
     * @throws \App\Exceptions\BookedModelNotFoundException
     */
    public function post(string $uri, Arrayable|array $data = []): Response
    {
        $response = Http::acceptJson()
            ->retry(3, 100, function ($exception) {
                if ($retry = $exception instanceof InvalidCredentialsException) {
                    $this->refreshAuth();
                }

                return $retry;
            }, false)
            ->withHeaders($this->getAuthHeaders())
            ->post(
                url: $this->generateUri($uri),
                data: $data instanceof Arrayable ? $data->toArray() : $data
            );

        if ($response->failed()) {
            $this->errorHandler->handle($response);
        }

        return $response;
    }

    public function get(string $uri, Arrayable|array $query = []): Response
    {
        $path = $this->generateUri($uri);

        $response = Http::acceptJson()
            ->retry(3, 100, function ($exception) {
                if ($retry = $exception instanceof InvalidCredentialsException) {
                    $this->refreshAuth();
                }

                return $retry;
            }, false)
            ->withHeaders($this->getAuthHeaders())
            ->get(
                $path,
                $query instanceof Arrayable ? $query->toArray() : $query
            );

        if ($response->failed()) {
            $this->errorHandler->handle($response, $path);
        }

        return $response;
    }

    public function delete(string $uri): Response
    {
        return Http::acceptJson()
            ->withHeaders($this->getAuthHeaders())
            ->delete($this->generateUri($uri));
    }

    private function generateUri(string $append): string
    {
        return $this->config->endpoint . (Str::startsWith($append, '/') ? $append : "/$append");
    }
}
