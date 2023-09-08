<?php

namespace App\Actions;

use App\Booked\Repositories\ReservationRepository;
use App\Models\PendingReservation;

class DeletePendingReservationAction extends BaseAction
{
    public function __construct(
        private ReservationRepository $repository
    ) {}

    public function execute(PendingReservation $reservation)
    {
        $deleted = $this->repository->destroy($reservation->reference_number);

        if ($deleted->successful()) {
            $reservation->delete();

            return true;
        }

        return false;
    }
}
