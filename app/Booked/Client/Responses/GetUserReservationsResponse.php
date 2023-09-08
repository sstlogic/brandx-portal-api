<?php

namespace App\Booked\Client\Responses;

use App\Booked\Models\BookedReservation;
use Illuminate\Support\Collection;

class GetUserReservationsResponse extends BookedResponse
{
    public function reservations(): Collection
    {
        return collect($this->json('reservations'))
            ->map(fn (array $data) => new BookedReservation($data));
    }
}
