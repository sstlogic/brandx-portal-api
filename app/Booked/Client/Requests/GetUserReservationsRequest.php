<?php

namespace App\Booked\Client\Requests;

use Carbon\Carbon;
use Illuminate\Contracts\Support\Arrayable;

class GetUserReservationsRequest implements Arrayable
{
    public function __construct(
        public int     $userId,
        public ?Carbon $start = null,
        public ?Carbon $end = null,
    ) {}

    public function toArray(): array
    {
        return [
            'userId' => $this->userId,
            'startDateTime' => $this->start?->format('Y-m-d'),
            'endDateTime' => $this->end?->format('Y-m-d'),
        ];
    }
}
