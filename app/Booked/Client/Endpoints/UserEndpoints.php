<?php

namespace App\Booked\Client\Endpoints;

use App\Booked\Client\Responses\CreateUserResponse;
use Illuminate\Http\Client\Response;

trait UserEndpoints
{
    /**
     * @throws \App\Exceptions\BookedModelNotFoundException
     */
    public function createUser(array $data): CreateUserResponse
    {
        $response = $this->post("/Users/", $data);

        return new CreateUserResponse($response);
    }

    public function updateUser(int $id, array $data)
    {
        $response = $this->post("/Users/$id", $data);

        return new CreateUserResponse($response);
    }

    public function updatePassword(int $id, string $current, string $new)
    {
        return $this->post("/Users/$id/Password", [
            'password' => $new,
        ]);
    }

    public function getUserByEmail(string $email): ?array
    {
        $response = $this->get("/Users/", [
            'email' => $email,
        ])->collect('users');

        return $response->where('emailAddress', $email)->first();
    }

    public function deleteUser(int $userId): Response
    {
        return $this->delete("/Users/$userId");
    }
}
