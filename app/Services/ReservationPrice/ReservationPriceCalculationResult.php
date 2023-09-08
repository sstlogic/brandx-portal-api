<?php

namespace App\Services\ReservationPrice;

class ReservationPriceCalculationResult
{
    public function __construct(
        public readonly float $price,
        public readonly float|int $rate,
        public readonly float $duration,
        public readonly string $band,
        public readonly string $type,
        public readonly bool $discount = false,
        public readonly ?string $discountType = null
    ) {}
}
