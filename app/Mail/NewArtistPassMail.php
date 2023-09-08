<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class NewArtistPassMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(public User $user) {}

    public function build()
    {
        return $this
            ->subject('Brand X Artist Pass Confirmation')
            ->cc(config('brandx.admin_email'))
            ->with([
                'user' => $this->user,
                'login' => config('brandx.frontend_url'),
            ])
            ->markdown('emails.new-artist-pass');
    }
}
