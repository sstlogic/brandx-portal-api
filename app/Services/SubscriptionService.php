<?php

namespace App\Services;

use App\Models\User;

class SubscriptionService
{
    public function getPrice(User $user)
    {
        if ($user->organisation) {
            return $this->organisation()['price_id'];
        }

        return $this->individual()['price_id'];
    }

    public function getPriceAmount(User $user)
    {
        if ($user->organisation) {
            return $this->organisation()['price'];
        }

        return $this->individual()['price'];
    }

    public function individual()
    {
        return config('brandx.subscriptions.artist-pass_individual');
    }

    public function organisation()
    {
        return config('brandx.subscriptions.artist-pass_organisation');
    }
}
