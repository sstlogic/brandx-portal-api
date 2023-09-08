<?php

namespace App\Services\ReservationPrice;

use App\Models\PendingReservation;
use Illuminate\Support\Collection;

class ReservationPriceService
{
    public function calculateFor(PendingReservation $reservation): ReservationPriceCalculationResult
    {
        return $reservation->isRepeating()
            ? $this->calculateRepeating($reservation, $reservation->repeatingInstances())
            : $this->calculate($reservation);
    }

    public function calculate(PendingReservation $reservation): ReservationPriceCalculationResult
    {
        if ($reservation->isRepeating()) {
            return $this->calculateRepeating($reservation, $reservation->repeatingInstances());
        }

        $band = $this->getPriceBand($reservation);

        $type = $this->calculateType($reservation);

        $duration = $reservation->durationInHours();

        $rate = $band->price($type);

        $price = round($rate * $duration, 2);

        return new ReservationPriceCalculationResult($price, $rate, $duration, $band->band, $type);
    }

    public function calculateRepeating(
        PendingReservation $reservation,
        ?Collection        $reservations
    ): ReservationPriceCalculationResult {
        $reservations = $reservations ?? $reservation->repeatingInstances();

        $band = $this->getPriceBand($reservations->first());

        $type = $this->calculateType($reservation, $reservations);

        $duration = $reservations->sum(function (PendingReservation $reservation) {
            return $reservation->durationInHours();
        });

        $rate = $band->price($type);

        $price = round($rate * $duration, 2);

        return new ReservationPriceCalculationResult($price, $rate, $duration, $band->band, $type);
    }

    private function getPriceBand(PendingReservation $reservation): PriceBand
    {
        if ($reservation->isForPerformance()) {
            return new PriceBand('performance');
        }

        if ($reservation->isCommercial()) {
            return new PriceBand('commercial');
        }

        if ($reservation->isSolo()) {
            return new PriceBand('solo');
        }

        // Reservation now can't be solo - so must be for more than one person

        if ($reservation->isFunded()) {
            return new PriceBand('funded');
        }

        // only remaining option is that the reservation is for more than one person, and is unfunded
        return new PriceBand('unfunded');
    }

    private function calculateType(PendingReservation $parent, ?Collection $reservations = null): string
    {
        if ($reservations && $parent->isRepeating()) {
            $diffInWeeks = $parent->start->copy()->startOf('week')->diffInWeeks($parent->end->copy()->endOfWeek());

            $duration = $reservations->sum(function (PendingReservation $reservation) {
                return $reservation->durationInHours();
            });

            if ($parent->isRepeatingWeeklyOnMultipleDays()) {
                if ($duration > (40 * $diffInWeeks)) {
                    return 'weekly';
                }
            }

            if ($parent->isRepeatingDaily()) {
                if ($duration / ($diffInWeeks > 0 ? $diffInWeeks : 1) > 40) {
                    return 'weekly';
                }
            }
        }

        if ($parent->durationInHours() >= 8) {
            return 'daily';
        }

        return 'hourly';
    }
}
