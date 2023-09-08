<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class BookingConfirmationMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User       $user,
        public string     $reference,
        public Collection $reservations
    ) {}

    public function build()
    {
        return $this
            ->with([
                'user' => $this->user,
                'reservations' => $this->reservations,
            ])
            ->subject('Your Brand X booking is confirmed')
            ->markdown('emails.booking-confirmation');
    }
}
