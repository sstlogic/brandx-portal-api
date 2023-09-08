<?php

namespace App\Jobs;

use App\Booked\Repositories\ReservationRepository;
use App\Models\PendingReservation;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteUnpaidBookingsJob
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    private ReservationRepository $repository;

    public function __construct() {}

    public function handle(ReservationRepository $repository)
    {
        $this->repository = $repository;

        $limit = Carbon::now()->subMinutes(10);

        $reservations = PendingReservation::query()->notPaid()->where('created_at', '<', $limit)->get();

        $reservations->map(fn (PendingReservation $reservation) => $this->deleteReservation($reservation));
    }

    private function deleteReservation(PendingReservation $reservation)
    {
        $response = $this->repository->destroy($reservation->reference_number);

        if ($response->successful()) {
            $reservation->delete();
        } else {
            $this->fail($response->toException());
        }
    }
}
