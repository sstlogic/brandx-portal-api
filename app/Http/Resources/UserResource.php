<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin \App\Models\User */
class UserResource extends JsonResource
{
    /**
     * @param  Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'uuid' => $this->uuid,
            'firstName' => $this->first_name,
            'lastName' => $this->last_name,
            'email' => $this->email,
            'phone' => $this->phone,
            'member' => $this->isMember(),
            'memberSince' => $this->memberSince()?->format('c'),
            'memberExpiry' => $this->memberExpiry(),
            'memberRenewal' => $this->memberRenewalDate(),
            'autoRenew' => ! $this->subscription()?->canceled(),
            'last4' => $this->pm_last_four,
            'existingMember' => $this->isExistingMember(),
            'organisation' => (bool) $this->organisation,
        ];
    }
}
