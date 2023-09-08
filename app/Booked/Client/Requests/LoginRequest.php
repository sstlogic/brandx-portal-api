<?php

namespace App\Booked\Client\Requests;

use Illuminate\Contracts\Support\Arrayable;

class LoginRequest implements Arrayable
{
    public function __construct(
        public string $username,
        public string $password
    ) {}

    public function toArray(): array
    {
        return [
            'username' => $this->username,
            'password' => $this->password,
        ];
    }
}
