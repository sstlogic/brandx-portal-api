<?php

namespace App\Jobs;

use App\Models\PendingReservation;
use App\Services\ReportingService;
use App\Services\ReservationPrice\ReservationPriceService;
use DB;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class PopulatePreviousBookingsReportingDataJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function handle()
    {
        DB::transaction(function () {
            PendingReservation::whereNull('invoice_amount')->chunk(10, function (Collection $reservations) {
                $reservations->each(fn (PendingReservation $reservation) => $this->populateData($reservation));
            });
        });
    }

    private function populateData(PendingReservation $reservation)
    {
        $reservation->update([
            'instance_cost' => app(ReservationPriceService::class)
                    ->calculateFor($reservation)->price / $reservation->durationInHours() / 100,
            'rate_paid' => app(ReservationPriceService::class)->calculateFor($reservation)->rate / 100,
            'escac_total' => app(ReportingService::class)
                    ->escacRevenueForReservations(collect([$reservation])) / 100,
            'coscs_total' => app(ReportingService::class)
                    ->coscsRevenueForReservations(collect([$reservation])) / 100,
            'studio' => $reservation->getLocation(),
        ]);
    }
}
