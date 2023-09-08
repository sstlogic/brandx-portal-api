<?php

namespace App\Actions;

use App\Booked\Client\Responses\DeleteReservationResponse;
use App\Booked\Repositories\ReservationRepository;

class DeleteReservationAction extends BaseAction
{
    public function __construct(
        private ReservationRepository $repository
    ) {}

    public function execute(string $reference): DeleteReservationResponse
    {
        return $this->repository->destroy($reference);
    }
}
