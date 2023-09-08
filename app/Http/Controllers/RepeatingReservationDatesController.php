<?php

namespace App\Http\Controllers;

use App\Actions\GetRepeatingScheduleSlotsAction;
use App\Http\Requests\RepeatingReservationDatesRequest;
use App\Models\PendingReservation;
use App\Services\ReservationPrice\ReservationPriceService;
use Carbon\Carbon;

class RepeatingReservationDatesController extends Controller
{
    private RepeatingReservationDatesRequest $request;

    public function __invoke(RepeatingReservationDatesRequest $request, ReservationPriceService $service)
    {
        $this->request = $request;
        $pendingReservations = $this->getPendingReservations($request->validated());

        return $pendingReservations->map(function (PendingReservation $reservation) {
            return [
                'start_time' => $reservation->start,
                'end_time' => $reservation->end,
            ];
        })->sortBy('start_time')->values();
    }

    private function getPendingReservations(array $data)
    {
        $dates = app(GetRepeatingScheduleSlotsAction::class)->dates($data);

        $startTime = Carbon::parse($data['start_time']);
        $endTime = Carbon::parse($data['end_time']);

        return $dates->map(fn (Carbon $date) => $this->createReservation($date, $startTime, $endTime));
    }

    private function createReservation(Carbon $date, Carbon $startTime, Carbon $endTime)
    {
        $start = $date
            ->copy()
            ->setTImezone('Australia/Sydney')
            ->setTimeFromTimeString($startTime->format('H:i'));

        $end = $date
            ->copy()
            ->setTImezone('Australia/Sydney')
            ->setTimeFromTimeString($endTime->format('H:i'));

        return new PendingReservation(
            [
                'start' => $start,
                'end' => $end,
                'attendees' => $this->request->input('attendees'),
                'generating_income' => $this->request->input('generatingIncome'),
                'funded' => $this->request->input('funded'),
                'performance' => $this->request->input('performance'),
            ]
        );
    }
}
