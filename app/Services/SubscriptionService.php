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
        try {
            $userBooked = json_decode(json_encode($bookedUser), true);
            $account_type =  $userBooked['account_type'];

            if ($account_type == "Arts_Organisation") {
                return $this->organisation()['price'];
            }
            // if ($account_type == "Artist") {
            //     return $this->individual()['price'];
            // }

            return $this->individual()['price'];
        } catch (\Throwable $th) {
            return $this->individual()['price'];
        }
    }

    public function getRate(User $user, $bookedUser)
    {
        try {

            $userBooked = json_decode(json_encode($bookedUser), true);
            $account_type =  $userBooked['account_type'];

            if ($account_type == "Arts_Organisation") {
                return $this->getRateOrganisation()['rate'];
            }
            if ($account_type == "Individual") {
                return $this->getRateIndividual()['rate'];
            }

            return $this->getRateGeneralPublic()['rate'];
        } catch (\Throwable $th) {
            return $this->getRateGeneralPublic()['rate'];
        }
    }

    public function getRateIndividual()
    {
        return config('brandx.account_type.Individual');
    }

    public function getRateOrganisation()
    {
        return config('brandx.account_type.Arts_Organisation');
    }

    public function getRateGeneralPublic()
    {
        return config('brandx.account_type.General_Public');
    }

    public function individual()
    {
        return config('brandx.subscriptions.artist-pass_individual');
    }

    public function organisation()
    {
        return config('brandx.subscriptions.artist-pass_organisation');
    }

    // public function generalPublic()
    // {
    //     return config('brandx.subscriptions.artist-pass_general_public');
    // }
}
