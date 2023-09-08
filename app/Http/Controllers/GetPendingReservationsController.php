<?php

namespace App\Http\Controllers;

use App\Http\Resources\PendingReservationResource;
use App\Models\User;

class GetPendingReservationsController extends Controller
{
    public function __invoke(User $user)
    {
        $reservations = $user->pendingReservations()->notPaid()->get();

        return PendingReservationResource::collection($reservations);
    }
}
