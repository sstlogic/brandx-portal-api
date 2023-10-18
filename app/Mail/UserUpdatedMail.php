<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class UserUpdatedMail extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public function __construct(
        public User $user
    ) {
    }

    public function build()
    {
        return $this
            ->with([
                'user' => $this->user,
                'url' => config('brandx.frontend_url') . '/login',
            ])
            ->subject('Brand X Profile Updated')
            ->markdown('emails.user-updated');
    }
}
