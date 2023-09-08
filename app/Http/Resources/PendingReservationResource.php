<?php

namespace App\Http\Resources;

use App\Models\PendingReservation;
use App\Services\ReservationPrice\ReservationPriceService;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Models\PendingReservation
 */
class PendingReservationResource extends JsonResource
{
    public function toArray($request): array
    {
        $service = new ReservationPriceService();

        $price = $service->calculateFor($this->resource);

        return [
            'uuid' => $this->uuid,
            'referenceNumber' => $this->reference_number,
            'paidAt' => $this->paid_at,
            'duration' => $this->durationInHours(),
            'rate' => $price->rate,
            'price' => $price->price,
            'useType' => $price->band,
            'dates' => $this->isRepeating() ? $this->getRepeatingDates() : [
                [
                    'start' => $this->start->format('c'),
                    'end' => $this->end->format('c'),
                ],
            ],
            'resourceName' => $this->resource_name,
        ];
    }

    private function getRepeatingDates()
    {
        return $this->repeatingInstances()
            ->sortBy(function (PendingReservation $pendingReservation) {
                return $pendingReservation->start->unix();
            })->map(
                function (PendingReservation $reservation) {
                    return [
                        'start' => $reservation->start->format('c'),
                        'end' => $reservation->end->format('c'),
                    ];
                }
            )->all();
    }
}
