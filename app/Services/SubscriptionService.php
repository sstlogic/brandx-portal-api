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

    public function getPriceAmount(User $user, $bookedUser)
    {

        return $bookedUser;
        if ($user->organisation) {
            return $this->organisation()['price'];
        }
        if ($user->organisation) {
            return $this->generalPublic()['price'];
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

    public function generalPublic()
    {
        return config('brandx.subscriptions.artist-pass_organisation');
    }
}
