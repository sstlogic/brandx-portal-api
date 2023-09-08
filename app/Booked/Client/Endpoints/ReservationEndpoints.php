<?php

namespace App\Booked\Client\Endpoints;

use App\Booked\Client\Requests\GetUserReservationsRequest;
use App\Booked\Client\Responses\CreateReservationResponse;
use App\Booked\Client\Responses\DeleteReservationResponse;
use App\Booked\Client\Responses\GetUserReservationsResponse;
use App\Booked\Models\BookedReservation;
use Illuminate\Http\Client\Response;

trait ReservationEndpoints
{
    public function getUserReservations(GetUserReservationsRequest $request): GetUserReservationsResponse
    {
        $response = $this->get(
            uri: "/Reservations/",
            query: $request
        );

        return new GetUserReservationsResponse($response);
    }

    public function findReservation(string $reference): ?BookedReservation
    {
        $response = $this->get(
            "/Reservations/$reference"
        );

        return new BookedReservation($response->json());
    }

    public function createReservation(array $data): CreateReservationResponse
    {
        $response = $this->post("/Reservations/", $data);

        return new CreateReservationResponse($response);
    }

    public function deleteReservation(string $referenceNumber): DeleteReservationResponse
    {
        $response = $this->delete("/Reservations/$referenceNumber");

        return new DeleteReservationResponse($response);
    }

    public function updateReservation(string $referenceNumber, array $data): Response
    {
        return $this->post("/Reservations/$referenceNumber", $data);
    }
}
