<?php

namespace App\Booked\Client;

use App\Booked\Client\Requests\LoginRequest;
use App\Booked\Client\Responses\LoginResponse;
use App\Booked\Models\BookedUser;
use App\Booked\Repositories\UserRepository;

class BookedAuthClient
{
    use BookedEndpoints;

    public function __construct(
        private BookedClient   $client,
        private UserRepository $userRepository
    ) {}

    public function login(string $username, string $password): ?BookedUser
    {
        $request = new LoginRequest($username, $password);

        $response = LoginResponse::make(
            $this->client->post(self::$login, $request)->json()
        );

        if ($response->isAuthenticated) {
            return $this->userRepository->find($response->userId);
        }

        return null;
    }
}
