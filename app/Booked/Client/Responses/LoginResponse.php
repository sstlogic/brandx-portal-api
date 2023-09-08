<?php

namespace App\Booked\Client\Responses;

class LoginResponse
{
    public function __construct(
        public ?int $userId,
        public bool $isAuthenticated
    ) {}

    public static function make(array $data): static
    {
        return new static(
            userId: $data['userId'],
            isAuthenticated: $data['isAuthenticated']
        );
    }
}
