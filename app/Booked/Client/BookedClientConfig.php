<?php

namespace App\Booked\Client;

class BookedClientConfig
{
    public function __construct(
        public readonly string $endpoint,
        public readonly string $username,
        public readonly string $password
    ) {}

    public function getCredentials(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}


