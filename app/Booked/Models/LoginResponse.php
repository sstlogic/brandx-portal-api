<?php

namespace App\Booked\Models;

use Carbon\Carbon;

class LoginResponse
{
    public function __construct(
        public readonly ?string $sessionToken,
        public readonly ?Carbon $sessionExpires,
        public readonly ?string $userId,
        public readonly bool $isAuthenticated,
        public readonly ?string $version,
        public readonly mixed $links,
        public readonly ?string $message,
        public readonly Carbon $attemptTime,
    ) {}

    public static function fromRequest(array $data): static
    {
        return new static(
            $data['sessionToken'],
            Carbon::parse($data['sessionExpires']),
            $data['userId'],
            $data['isAuthenticated'],
            $data['version'],
            $data['links'],
            $data['message'],
            Carbon::now()
        );
    }

    public function isLoggedIn(): bool
    {
        return $this->isAuthenticated;
    }

    public function timeUntilExpiry(): ?int
    {
        if (! $this->sessionExpires) {
            return null;
        }

        return (int) $this->attemptTime->diffInSeconds($this->sessionExpires);
    }
}
