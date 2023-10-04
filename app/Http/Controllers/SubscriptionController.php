<?php

namespace App\Http\Controllers;

use App\Mail\NewArtistPassMail;
use App\Booked\Repositories\UserRepository;
use App\Models\User;
use App\Http\Resources\BookedUserResource;
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

    public function store(Request $request, SubscriptionService $service, UserRepository $repository)
    {
        $user = $request->user();

        $user->createOrGetStripeCustomer();

        $sub = $user->newSubscription('default', $service->getPrice($user))
            ->create($request->input('paymentMethodId'));

        $bookedUser = $repository->find($user->external_id);
        $userAttribute = new BookedUserResource($bookedUser);
        $rate = $service->getRate($user, $userAttribute);
        $saving = 66 - $rate;
        $otherDetails = [
            'rate' => $rate,
            'saving' => number_format($saving, 2),
            'company_name' => $userAttribute->organisationName(),
            'join_date' => $user->created_at->format('d/m/Y'),
            'expiry_date' => $user->memberExpiry()?->format('d/m/Y'),
        ];

        Mail::to($user)->send(new NewArtistPassMail($user, $otherDetails));

        return new JsonResponse([
            'reference' => $sub->latestInvoice()?->number,
            'memberExpiry' => $user->memberExpiry()?->format('c'),
            'nextBill' => $user->memberExpiry()?->format('c'),
            'user' => $user,
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

    public function price(User $user, SubscriptionService $service, UserRepository $repository)
    {
        $bookedUser = $repository->find($user->external_id);
        $userAttribute = new BookedUserResource($bookedUser);

        return $service->getPriceAmount($user, $userAttribute);
    }
}
