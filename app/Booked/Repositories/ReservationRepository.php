<?php

namespace App\Booked\Repositories;

use App\Booked\Client\Requests\GetUserReservationsRequest;
use App\Booked\Client\Responses\CreateReservationResponse;
use App\Booked\Client\Responses\DeleteReservationResponse;
use App\Booked\Models\BookedReservation;
use Carbon\Carbon;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Collection;

class ReservationRepository extends BaseRepository
{
    public function forUser(int $userId, ?Carbon $start = null, ?Carbon $end = null): Collection
    {
        $request = new GetUserReservationsRequest(
            userId: $userId,
            start: $start,
            end: $end
        );

        return $this
            ->client
            ->getUserReservations($request)
            ->reservations();
    }

    public function find(string $reference): ?BookedReservation
    {
        return $this->client->findReservation($reference);
    }

    public function create(array $data): CreateReservationResponse
    {
        return $this->client->createReservation($data);
    }

    public function destroy(string $referenceNumber): DeleteReservationResponse
    {
        return $this->client->deleteReservation($referenceNumber);
    }

    public function update(string $referenceNumber, array $data): Response
    {
        return $this->client->updateReservation($referenceNumber, $data);
    }
}
