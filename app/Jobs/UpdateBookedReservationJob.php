<?php

namespace App\Jobs;

use App\Booked\Models\BookedReservation;
use App\Booked\Repositories\AttributeRepository;
use App\Booked\Repositories\ReservationRepository;
use App\Models\PendingReservation;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class UpdateBookedReservationJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        private PendingReservation $pendingReservation,
        private array              $data = []
    ) {}

    public function handle(ReservationRepository $repository)
    {
        $reservation = $repository->find($this->pendingReservation->reference_number);

        if (! $reservation) {
            $this->fail(new Exception("Reservation does not exist in Booked."));
        }

        $repository->update(
            $this->pendingReservation->reference_number,
            $this->buildPayload($reservation)
        );
    }

    public function buildPayload(BookedReservation $bookedReservation)
    {
        $userId = $bookedReservation->userId();

        if (! $userId) {
            throw new Exception("Can not update booking if User ID is not set.");
        }

        return [
            'resourceId' => $this->pendingReservation->resource_id,
            'startDateTime' => $bookedReservation->startDate,
            'endDateTime' => $bookedReservation->endDate,
            'userId' => $userId,
            'customAttributes' => $bookedReservation->mergeCustomAttributes(
                $this->buildAttributes()
            ),
        ];
    }

    private function buildAttributes()
    {
        $repository = app(AttributeRepository::class);

        $attributes = [
            'payment_status' => $this->pendingReservation->payment_status,
            'paid_at' => $this->pendingReservation->paid_at,
            'payment_reference' => $this->pendingReservation->payment_reference,
            'invoice_id' => $this->pendingReservation->invoice_id,
            'invoice_amount' => $this->pendingReservation->invoice_amount,
            'rate_paid' => $this->pendingReservation->rate_paid,
            'escac_total' => $this->pendingReservation->escac_total,
            'coscs_total' => $this->pendingReservation->coscs_total,
            'studio' => $this->pendingReservation->studio,
        ];

        return collect($attributes)
            ->map(function ($attribute, $key) use ($repository) {
                return [
                    'attributeId' => $repository->reservationAttributeId($key),
                    'attributeValue' => $attribute,
                ];
            })
            ->reject(function ($array) {
                return ! $array['attributeId'];
            })
            ->values()
            ->all();
    }
}
