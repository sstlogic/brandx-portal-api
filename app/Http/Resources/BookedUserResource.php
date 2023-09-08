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
            'insurance' => $this->getAttribute('insurance'),
        ];
    }
}
