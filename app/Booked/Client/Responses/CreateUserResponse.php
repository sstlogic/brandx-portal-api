<?php

namespace App\Booked\Client\Responses;

use Illuminate\Contracts\Support\Arrayable;

class CreateUserResponse extends BookedResponse implements Arrayable
{
    public function userId(): ?int
    {
        return $this->json('userId');
    }

    public function successful(): bool
    {
        return parent::successful() && ! is_null($this->userid());
    }

    public function toArray(): array
    {
        return [
            'userId' => $this->userId(),
        ];
    }
}
