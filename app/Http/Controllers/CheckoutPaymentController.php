<?php

namespace App\Http\Controllers;

use App\Jobs\UpdateBookedReservationJob;
use App\Mail\BookingConfirmationMail;
use App\Models\PendingReservation;
use App\Models\User;
use App\Services\ReportingService;
use App\Services\ReservationPrice\ReservationPriceCalculationResult;
use App\Services\ReservationPrice\ReservationPriceService;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use DB;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Mail;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class CheckoutPaymentController extends Controller
{
    public function __construct(
        private ReservationPriceService $service
    ) {}

    public function __invoke(User $user)
    {
        return DB::transaction(function () use ($user) {
            $reservations = $user->pendingReservations()->notPaid()->get();

            if ($reservations->isEmpty()) {
                throw new BadRequestException("All reservations have been paid.");
            }

            $charges = $reservations->map(function (PendingReservation $reservation) {
                return $this->createCharge($reservation);
            });

            collect($charges)->each(function ($charge) use ($user) {
                $user->tab(
                    $charge['description'],
                    $charge['total'],
                    [
                        'currency' => 'aud',
                        'tax_rates' => [
                            config('brandx.tax_rate'),
                        ],
                    ]
                );
            });

            try {
                $invoice = $user->invoice([
                    'metadata' => $this->generateInvoiceMetaData($user, $reservations),
                ]);
            } catch (IncompletePayment $exception) {
                $payment = $exception->payment;
                $invoice = $payment->asStripePaymentIntent()->invoice;
                $invoice->markUncollectible();
                Bugsnag::notifyException($exception);

                // TODO - handle incomplete payments
                // if ($exception->payment->requiresAction() || $exception->payment->requiresConfirmation()) {
                //     return new JsonResponse([
                //         'message' => 'incomplete',
                //         'redirect' => route('cashier.payment', [
                //             'id' => $exception->payment->id,
                //             'redirect' => config('brandx.frontend_url') . '/bookings',
                //         ]),
                //     ], 400);
                // }

                abort(400, $exception->getMessage());
            }

            $reservations->map(function (PendingReservation $reservation) use ($invoice) {
                $reservation->update([
                    'paid_at' => Carbon::now(),
                    'payment_reference' => $invoice->asStripeInvoice()->number,
                    'payment_status' => PendingReservation::PAID,
                    'invoice_id' => $invoice->asStripeInvoice()->id,
                    'invoice_amount' => $invoice->total / 100,
                ]);

                UpdateBookedReservationJob::dispatch($reservation);
            });

            Mail::to($user->email)->send(
                new BookingConfirmationMail($user, $invoice->asStripeInvoice()->number, $reservations)
            );

            return new JsonResponse([
                'reference' => $invoice->asStripeInvoice()->number,
                'total' => $invoice->rawTotal(),
            ]);
        });
    }

    private function createCharge(PendingReservation $reservation)
    {
        $price = $this->service->calculateFor($reservation);

        return [
            'total' => $price->price,
            'room' => $reservation->resource_name,
            'type' => $price->band,
            'units' => $reservation->durationInHours(),
            'description' => $this->buildDescription($reservation, $price),
        ];
    }

    private function buildDescription(PendingReservation $reservation, ReservationPriceCalculationResult $price)
    {
        if ($reservation->isRepeating()) {
            return sprintf(
                "Repeating %s - every %s, starting on %s - %s. %s (%s) - $%s/hour for %s hours at %s",
                $reservation->prettyIntervalType(),
                $reservation->prettyInterval(),
                $reservation->repeatingInstances()->first()->start->setTimezone('Australia/Sydney')->format(
                    'd/m/Y g:ia'
                ),
                $reservation->repeatingInstances()->first()->end->setTimezone('Australia/Sydney')->format(
                    'g:ia'
                ),
                ucfirst($price->band),
                ucfirst($price->type),
                ($price->rate) / 100,
                $price->duration,
                $reservation->resource_name
            );
        }

        return sprintf(
            "%s. %s (%s) - $%s/hour for %s hours at %s",
            $reservation->start->setTimezone('Australia/Sydney')->format('d/m/Y g:ia'),
            ucfirst($price->band),
            ucfirst($price->type),
            ($price->rate) / 100,
            $price->duration,
            $reservation->resource_name
        );
    }

    private function generateInvoiceMetaData(User $user, Collection $reservations)
    {
        return [
            'email' => $user->email,
            'name' => $user->first_name . ' ' . $user->last_name,
            'booked_user_ref' => $user->external_id,
            'escac_total' => app(ReportingService::class)->escacRevenueForReservations($reservations) / 100,
            'coscs_total' => app(ReportingService::class)->coscsRevenueForReservations($reservations) / 100,
        ];
    }
}
