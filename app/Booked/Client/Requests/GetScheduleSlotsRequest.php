<?php

namespace App\Booked\Client\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class GetScheduleSlotsRequest implements Arrayable
{
    public Carbon $start;

    public Carbon $end;

    public ?int $resourceId = null;

    public function __construct(?Carbon $start = null, ?Carbon $end = null, ?int $resourceId = null)
    {
        $this->start = $start ?? Carbon::now()->startOfWeek();
        $this->end = $end ?? Carbon::now()->endOfWeek();
        $this->resourceId = $resourceId;
    }

    public function toArray(): array
    {
        return [
            'startDateTime' => $this->start->format('Y-m-d'),
            'endDateTime' => $this->end->format('Y-m-d'),
            'resourceId' => $this->resourceId,
        ];
    }
}
