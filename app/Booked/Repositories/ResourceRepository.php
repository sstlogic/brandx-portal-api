<?php

namespace App\Booked\Repositories;

use App\Booked\Models\BookedResource;
use App\Booked\Models\BookedResourceStatus;
use App\Booked\Models\BookedResourceStatusReason;
use App\Facades\Booked;
use Illuminate\Support\Collection;

class ResourceRepository extends BaseRepository
{
    public function all(): Collection
    {
        $response = $this->client->get("/Resources/")->json();

        return collect($response['resources'])->map(fn (array $data) => new BookedResource($data));
    }

    public function find(int $id): ?BookedResource
    {
        $response = $this->client->get("/Resources/$id")->json();

        return new BookedResource($response);
    }

    public function statuses(): Collection
    {
        $response = Booked::get("/Resources/Status")->json();

        return collect($response['statuses'])->map(fn (array $data) => new BookedResourceStatus($data));
    }

    public function statusReasons(): Collection
    {
        $response = Booked::get("/Resources/Status/Reasons")->json();

        return collect($response['reasons'])->map(fn (array $data) => new BookedResourceStatusReason($data));
    }
}
