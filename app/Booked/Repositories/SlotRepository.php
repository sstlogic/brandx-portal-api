<?php

namespace App\Booked\Repositories;

use App\Booked\Collections\SlotCollection;
use App\Booked\Models\BookedResource;
use App\Booked\Models\BookedSchedule;
use Carbon\Carbon;

class SlotRepository extends BaseRepository
{
    public function forSchedule(
        BookedSchedule|int $schedule,
        ?Carbon            $start = null,
        ?Carbon            $end = null,
        ?int               $resourceId = null
    ): SlotCollection {
        $id = $schedule instanceof BookedSchedule
            ? $schedule->id
            : $schedule;

        $response = $this->client->getScheduleSlots($id, $start, $end, $resourceId);

        return $response->slots();
    }

    public function forResource(
        BookedResource $resource,
        ?Carbon        $start = null,
        ?Carbon        $end = null,
    ): SlotCollection {
        $schedule = $resource->scheduleId;

        return $this->forSchedule($schedule, $start, $end, $resource->resourceId);
    }
}
