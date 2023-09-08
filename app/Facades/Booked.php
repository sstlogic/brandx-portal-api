<?php

namespace App\Facades;

use App\Booked\Client\BookedClient;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Facade;

/**
 * @method static BookedClient getInstance()
 * @method static Response post(string $uri, array|Arrayable $data = [])
 * @method static Response get(string $uri, array|Arrayable $data = [])
 * @method static Response delete(string $uri)
 *
 * @mixin BookedClient
 */
class Booked extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return BookedClient::class;
    }
}
