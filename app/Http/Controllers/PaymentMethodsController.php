<?php

namespace App\Http\Controllers;

use Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class PaymentMethodsController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        return $user->paymentMethods();
    }

    public function store(Request $request)
    {
        $user = Auth::user();

        $user->createOrGetStripeCustomer();

        $user->addPaymentMethod($request->input('payment_method'));

        if (count($user->paymentMethods()) === 1) {
            $user->updateDefaultPaymentMethod($request->input('payment_method'));
        }

        return response('Payment method added', 201);
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
        $user = Auth::user();

        if (count($user->paymentMethods()) === 1) {
            throw new BadRequestException("User must have at least one payment method.");
        }

        $user->deletePaymentMethod($id);

        return response('Deleted', 200);
    }

    public function setup()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        return new JsonResponse([
            'intent' => $user->createSetupIntent(),
        ]);
    }

    public function default()
    {
        $user = Auth::user();

        return $user->defaultPaymentMethod();
    }

    public function setDefault(Request $request)
    {
        $request->validate([
            'id' => ['required', 'string']
        ]);

        $user = Auth::user();

        $user->updateDefaultPaymentMethod($request->input('id'));

        return response('Default updated', 201);
    }
}
