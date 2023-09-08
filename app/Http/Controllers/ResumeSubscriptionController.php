<?php

namespace App\Http\Controllers;

use App\Mail\AutoRenewOnMail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class ResumeSubscriptionController extends Controller
{
    public function __invoke()
    {
        $user = Auth::user();

        $user->subscription()->resume();

        Mail::to($user->email)->send(new AutoRenewOnMail($user));

        return response('Subscription resumed.', 200);
    }
}
