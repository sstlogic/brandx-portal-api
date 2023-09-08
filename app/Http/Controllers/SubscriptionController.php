<?php

namespace App\Http\Controllers;

use App\Mail\NewArtistPassMail;
use App\Models\User;
use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Mail;

class SubscriptionController extends Controller
{
    public function index()
    {
        //
    }

    public function store(Request $request, SubscriptionService $service)
    {
        $user = $request->user();

        $user->createOrGetStripeCustomer();

        $sub = $user->newSubscription('default', $service->getPrice($user))
            ->create($request->input('paymentMethodId'));

        Mail::to($user)->send(new NewArtistPassMail($user));

        return new JsonResponse([
            'reference' => $sub->latestInvoice()?->number,
            'memberExpiry' => $user->memberExpiry()?->format('c'),
            'nextBill' => $user->memberExpiry()?->format('c'),
        ], 201);
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

    public function price(User $user, SubscriptionService $service)
    {
        return $service->getPriceAmount($user);
    }
}
