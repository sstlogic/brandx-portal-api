<?php

namespace App\Booked\Repositories;

use App\Booked\Models\BookedUser;
use App\Facades\Booked;
use Illuminate\Support\Collection;

class UserRepository extends BaseRepository
{
    /**
     * @return \Illuminate\Support\Collection<BookedUser>
     */
    public function all(): Collection
    {
        $response = Booked::get('/Users/')->json();

        return collect($response['users'])
            ->map(fn (array $data) => new BookedUser($data));
    }

    public function find(int $id): ?BookedUser
    {
        $response = Booked::get("/Users/$id")->json();

        return new BookedUser($response);
    }

    public function findByEmail(string $email): ?BookedUser
    {
        $user = $this->client->getUserByEmail($email);

        return $user
            ? new BookedUser($user)
            : null;
    }

    public function create(array $data): ?BookedUser
    {
        $response = $this->client->createUser($data);

        return $response->successful()
            ? $this->find($response->userId())
            : null;
    }

    public function update(int $id, array $data): ?BookedUser
    {
        $response = $this->client->updateUser($id, $data);

        return $response->successful()
            ? $this->find($response->userId())
            : null;
    }

    public function updatePassword(int $id, string $current, string $new)
    {
        $response = $this->client->updatePassword($id, $current, $new);

        return $response->successful();
    }

    public function delete(BookedUser|int $user): bool
    {
        return $this
            ->client
            ->deleteUser($user instanceof BookedUser ? $user->id : $user)
            ->successful();
    }
}
