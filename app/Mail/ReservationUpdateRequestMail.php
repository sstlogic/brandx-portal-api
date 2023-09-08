<?php

namespace App\Mail;

use App\Booked\Models\BookedReservation;
use App\Booked\Repositories\ReservationRepository;
use App\Models\PendingReservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ReservationUpdateRequestMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User  $user,
        public array $data
    ) {}

    public function build()
    {
        $bookedReservation = $this->getBookedReservation();

        return $this
            ->with([
                'submitted_ref' => $this->data['reference'],
                'submitted_name' => $this->data['name'],
                'submitted_message' => $this->data['message'],
                'submitted_email' => $this->data['email'],
                'user' => $this->user,
                'booking_exists' => ! is_null($bookedReservation),
                'start_date' => $bookedReservation?->startDate->setTimezone('Australia/Sydney')->format('d/m/Y g:ia'),
                'end_date' => $bookedReservation?->endDate->setTimezone('Australia/Sydney')->format('d/m/Y g:ia'),
                'url' => $this->buildUrl($bookedReservation),
                'user_url' => $this->buildUserUrl(),
                'stripe_url' => $this->getStripeInvoiceUrl($bookedReservation),
            ])
            ->replyTo($this->user->email)
            ->subject('Booking Modification Request')
            ->markdown('emails.reservation-update-request');
    }

    public function getBookedReservation(): ?BookedReservation
    {
        $repository = app(ReservationRepository::class);

        return $repository->find($this->data['reference']);
    }

    private function buildUrl(?BookedReservation $bookedReservation): string
    {
        if (! $bookedReservation) {
            return '';
        }

        return config('brandx.booked_url') . "/reservation.php?rn={$bookedReservation->referenceNumber}";
    }

    private function buildUserUrl(): string
    {
        return config('brandx.booked_url') . "/admin/manage_users.php?uid=" . $this->user->external_id;
    }

    private function getStripeInvoiceUrl(?BookedReservation $bookedReservation): string
    {
        if (! $bookedReservation) {
            return '';
        }

        $pendingReservation = PendingReservation::where('reference_number', $bookedReservation->referenceNumber)
            ->first();

        return "https://dashboard.stripe.com/invoices/" . $pendingReservation->invoice_id;
    }
}
