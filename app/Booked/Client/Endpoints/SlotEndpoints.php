<?php

namespace App\Booked\Client\Endpoints;

use App\Booked\Client\Requests\GetScheduleSlotsRequest;
use App\Booked\Client\Responses\GetScheduleSlotsResponse;
use Carbon\Carbon;

trait SlotEndpoints
{
    public function getScheduleSlots(
        int     $id,
        ?Carbon $start = null,
        ?Carbon $end = null,
        ?int    $resourceId = null
    ): GetScheduleSlotsResponse {
        $request = new GetScheduleSlotsRequest($start, $end, $resourceId);

        $response = $this->get("/Schedules/$id/Slots", $request)->json();

        return new GetScheduleSlotsResponse($response);
    }
}
