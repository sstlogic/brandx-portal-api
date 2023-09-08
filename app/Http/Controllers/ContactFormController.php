<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactFormRequest;
use App\Mail\ContactFormMail;
use Mail;

class ContactFormController extends Controller
{
    public function __invoke(ContactFormRequest $request)
    {
        Mail::to(config('brandx.admin_email'))->send(new ContactFormMail($request->validated()));

        return response('', 200);
    }
}
