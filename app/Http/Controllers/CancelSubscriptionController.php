<?php

namespace App\Http\Controllers;

use App\Mail\AutoRenewOffMail;
use Auth;
use Mail;

class CancelSubscriptionController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $user->subscription()->cancel();

        Mail::to($user->email)->send(new AutoRenewOffMail($user));

        return response('Subscription Canceled', 200);
    }
}
