<?php

namespace App\Services;

use App\Booked\Enums\ReservationLocation;
use App\Models\PendingReservation;
use App\Services\ReservationPrice\ReservationPriceService;
use Illuminate\Support\Collection;

class ReportingService
{
    public function __construct(
        private ReservationPriceService $priceService
    ) {}

    public function escacRevenueForReservations(Collection $collection)
    {
        return $collection->sum(function (PendingReservation $pendingReservation) {
            $location = $pendingReservation->getLocation();

            if (! $location || $location === ReservationLocation::Coscs) {
                return 0;
            }

            return $this->priceService->calculateFor($pendingReservation)->price;
        });
    }

    public function coscsRevenueForReservations(Collection $collection)
    {
        return $collection->sum(function (PendingReservation $pendingReservation) {
            $location = $pendingReservation->getLocation();

            if (! $location || $location === ReservationLocation::Escac) {
                return 0;
            }

            return $this->priceService->calculateFor($pendingReservation)->price;
        });
    }
}
