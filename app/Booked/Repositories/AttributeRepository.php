<?php

namespace App\Booked\Repositories;

use Cache;
use Illuminate\Support\Collection;

class AttributeRepository extends BaseRepository
{
    const RESERVATION_CATEGORY = 1;
    const USER_CATEGORY = 2;
    const RESOURCE_CATEGORY = 4;

    public function userAttributes(): Collection
    {
        // return Cache::remember('booked:user:attributes', now()->addHour(), function () {
        return $this->client->getCategoryAttributes(static::USER_CATEGORY);
        // });
    }

    public function resourceAttributes(): Collection
    {
        // return Cache::remember('booked:reservations:attributes', now()->addHour(), function () {
        return $this->client->getCategoryAttributes(static::RESERVATION_CATEGORY);
        // });
    }

    public function userAttributeId(string $attribute)
    {
        $attribute = $this->userAttributes()->where('label', $attribute)->first();

        return $attribute ? $attribute['id'] : null;
    }

    public function reservationAttributeId(string $attribute)
    {
        $attribute = $this->resourceAttributes()->where('label', $attribute)->first();

        return $attribute ? $attribute['id'] : null;
    }

    public function createReservationAttribute(string $attributeName)
    {
        return $this->client->createReservationAttribute($attributeName);
    }
}
