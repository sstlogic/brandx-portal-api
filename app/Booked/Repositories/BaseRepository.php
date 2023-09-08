<?php

namespace App\Booked\Repositories;

use App\Booked\Client\BookedClient;

class BaseRepository
{
    protected string $path;

    public function __construct(
        protected BookedClient $client
    ) {}
}
