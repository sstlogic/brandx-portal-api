<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateReservationRequest;
use App\Mail\ReservationUpdateRequestMail;
use Auth;
use Mail;

class UpdateReservationRequestController extends Controller
{
    public function __invoke(UpdateReservationRequest $request)
    {
        Mail::to(config('brandx.admin_email'))
            ->send(
                new ReservationUpdateRequestMail(Auth::user(), $request->validated())
            );

        return response('', 200);
    }
}
