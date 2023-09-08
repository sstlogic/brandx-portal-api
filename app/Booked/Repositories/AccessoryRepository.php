<?php

namespace App\Booked\Repositories;

use App\Booked\Models\BookedAccessory;
use App\Facades\Booked;
use Illuminate\Support\Collection;

class AccessoryRepository extends BaseRepository
{
    public function all(): Collection
    {
        $response = Booked::get('/Accessories/')->json();

        return collect($response['accessories'])->map(fn (array $data) => new BookedAccessory($data));
    }

    public function find(int $id): ?BookedAccessory
    {
        $response = Booked::get("/Accessories/$id")->json();

        if (! array_key_exists('id', $response)) {
            return null;
        }

        return new BookedAccessory($response);
    }
}
