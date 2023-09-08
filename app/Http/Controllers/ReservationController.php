<?php

namespace App\Http\Controllers;

use App\Booked\Repositories\ReservationRepository;
use App\Http\Resources\BookedReservationResource;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReservationController extends Controller
{
    public function index(ReservationRepository $repository)
    {
        $user = Auth::user();

        return BookedReservationResource::collection(
            $repository->forUser($user->external_id, Carbon::now()->startOfYear(), Carbon::now()->endOfYear())
        );
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
