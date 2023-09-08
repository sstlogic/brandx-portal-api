<?php

namespace App\Booked\Repositories;

use App\Booked\Models\BookedSchedule;
use Illuminate\Support\Collection;

class ScheduleRepository extends BaseRepository
{
    public function all(): Collection
    {
        $response = $this->client->get("/Schedules")->json();

        return collect($response['schedules'])->map(fn (array $data) => new BookedSchedule($data));
    }

    public function find(int $id): ?BookedSchedule
    {
        $response = $this->client->get("/Schedules/$id")->json();

        return new BookedSchedule($response);
    }
}
