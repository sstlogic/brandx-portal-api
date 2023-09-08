<?php

namespace App\Http\Controllers;

use App\Actions\CreatePendingReservationAction;
use App\Actions\DeletePendingReservationAction;
use App\Http\Requests\CreatePendingReservationRequest;
use App\Models\PendingReservation;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PendingReservationsController extends Controller
{
    public function index()
    {
        //
    }

    public function store(CreatePendingReservationRequest $request, CreatePendingReservationAction $action)
    {
        $action->execute($request->validated());

        return response('', 201);
    }

    public function show(PendingReservation $pendingReservation)
    {
        //
    }

    public function update(Request $request, PendingReservation $pendingReservation)
    {
        //
    }

    public function destroy(DeletePendingReservationAction $action, PendingReservation $reservation,)
    {
        $deleted = $action->execute($reservation);

        return new JsonResponse([
            'success' => $deleted,
        ]);
    }
}
