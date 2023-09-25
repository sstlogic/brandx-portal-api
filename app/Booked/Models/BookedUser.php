<?php

namespace App\Booked\Models;

use Carbon\Carbon;
use Illuminate\Support\Collection;

/**
 * @property string|int $id
 * @property string $username
 * @property string $firstName
 * @property string $lastName
 * @property string $emailAddress
 * @property ?string $phoneNumber
 * @property array $customAttributes
 * @property string $organization
 * @property array $groups
 */
class BookedUser extends BookedModel
{
    public function attributes(): Collection
    {
        return collect($this->customAttributes);
    }

    public function getAttribute(string $name): ?string
    {
        $attribute = $this->attributes()->where('label', $name)->first();

        return $attribute ? $attribute['value'] : null;
    }

    public function existingMemberExpiry(): ?Carbon
    {
        $expiry = $this->attributes()->where('label', 'expiry_date')->first();

        if (!$expiry) {
            return null;
        }

        return $expiry['value'] ? Carbon::parse($expiry['value']) : null;
    }

    public function organisationName(): ?string
    {
        return $this->organization;
    }

    public function organisationType(): ?string
    {
        return $this->getAttribute('org_type');
    }

    public function organisationAbn(): ?string
    {
        return $this->getAttribute('org_abn');
    }

    public function artform(): ?string
    {
        return $this->getAttribute('art_form');
    }

    // public function accountType(): ?string
    // {
    //     return $this->getAttribute('account_type');
    // }
}