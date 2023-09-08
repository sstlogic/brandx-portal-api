<?php

namespace App\Http\Controllers;

use App\Models\PendingReservation;
use App\Models\User;
use App\Services\ReservationPrice\ReservationPriceService;
use Illuminate\Http\JsonResponse;

class GetCheckoutDetailsController extends Controller
{
    public function __construct(
        private ReservationPriceService $priceService
    ) {}

    public function __invoke(User $user)
    {
        return new JsonResponse([
            'total' => $this->getTotalCheckoutAmount($user),
        ]);
    }

    private function getTotalCheckoutAmount(User $user)
    {
        $reservations = $user->pendingReservations()->notPaid()->orderBy('start')->get();

        return $reservations->sum(
            fn (PendingReservation $reservation) => $this->priceService->calculateFor($reservation)->price
        );
    }
}
