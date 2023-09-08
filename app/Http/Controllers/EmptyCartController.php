<?php

namespace App\Http\Controllers;

use App\Booked\Repositories\ReservationRepository;
use App\Models\PendingReservation;
use Illuminate\Support\Facades\Auth;
use Request;

class EmptyCartController extends Controller
{
    public function __construct(
        private ReservationRepository $repository
    ) {}

    public function __invoke(Request $request)
    {
        $user = Auth::user();

        $reservations = $user->pendingReservations()->get();
        $reservations->map(function (PendingReservation $reservation) {
            $deleted = $this->repository->destroy($reservation->reference_number);

            if ($deleted->successful()) {
                $reservation->delete();
            }
        });

        return true;
    }
}
