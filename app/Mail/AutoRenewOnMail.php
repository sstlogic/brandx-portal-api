<?php

namespace App\Mail;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AutoRenewOnMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        private User $user
    ) {}

    public function build()
    {
        $expiry = Carbon::parse($this->user->subscription()->asStripeSubscription()->current_period_end);

        return $this
            ->subject('Brand X Subscription Updated')
            ->with([
                'expiry' => $expiry->setTimezone('Australia/Sydney')->format('d/m/Y'),
            ])->markdown('emails.auto-renew-on');
    }
}
