<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReservationQuoteRequest;
use App\Models\PendingReservation;
use App\Services\ReservationPrice\ReservationPriceService;
use Illuminate\Http\JsonResponse;

class ReservationQuoteController extends Controller
{
    public function __invoke(ReservationQuoteRequest $request, ReservationPriceService $service)
    {
        $pendingReservation = new PendingReservation(
            [
                'start' => $request->date('start'),
                'end' => $request->date('end'),
                'attendees' => $request->input('attendees'),
                'generating_income' => $request->input('generatingIncome'),
                'funded' => $request->input('funded'),
                'performance' => $request->input('performance'),
            ]
        );

        $price = $service->calculateFor($pendingReservation);

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
}
