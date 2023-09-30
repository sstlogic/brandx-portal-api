<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Booked\Models\BookedUser
 */
class BookedUserResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'username' => $this->username,
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'organisationName' => $this->organisationName(),
            'organisationType' => $this->organisationType(),
            'organisationAbn' => $this->organisationAbn(),
            'artform' => $this->artForm(),
            'email' => $this->emailAddress,
            'address' => $this->getAttribute('address'),
            'suburb' => $this->getAttribute('suburb'),
            'state' => $this->getAttribute('state'),
            'country' => $this->getAttribute('country'),
            'postcode' => $this->getAttribute('postcode'),
            'phone' => $this->phoneNumber,
            'wk_ph' => $this->phoneNumber,
            'insurance' => $this->getAttribute('insurance'),
            'account_type' => $this->getAttribute('account_type'),
            'roleInOrg' => $this->getAttribute('role_in_org'),
            'accurate' => $this->getAttribute('accurate'),
            'website' => $this->getAttribute('website'),
            'promo' => $this->getAttribute('promo'),
        ];
    }
}