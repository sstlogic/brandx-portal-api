<?php

namespace App\Http\Controllers;

use App\Actions\GetRepeatingScheduleSlotsAction;
use App\Http\Requests\RepeatingReservationQuoteRequest;
use App\Models\PendingReservation;
use App\Services\ReservationPrice\ReservationPriceService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class RepeatingReservationQuoteController extends Controller
{
    private RepeatingReservationQuoteRequest $request;

    public function __invoke(RepeatingReservationQuoteRequest $request, ReservationPriceService $service)
    {
        $this->request = $request;
        $data = $request->validated();
        $pendingReservations = $this->getPendingReservations($data);

        $price = $service->calculateRepeating(
            new PendingReservation(
                [
                    'start' => Carbon::parse($data['start_date']),
                    'end' => Carbon::parse($data['end_date']),
                    'interval' => $data['interval'],
                    'interval_type' => $data['interval_type'],
                    'weekly_days' => $data['weekly_days'],
                    'start_time' => Carbon::parse($data['start_time']),
                    'end_time' => Carbon::parse($data['end_time']),
                ]
            ),
            $pendingReservations
        );

        return new JsonResponse([
            'rate' => $price->rate,
            'price' => $price->price,
            'duration' => $price->duration,
            'useType' => $price->band,
            'discount' => $price->discount,
            'discountType' => $price->discountType,
            'rateType' => $price->type,
        ]);
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
