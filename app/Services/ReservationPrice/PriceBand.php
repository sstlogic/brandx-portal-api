<?php

namespace App\Services\ReservationPrice;

class PriceBand
{
    private array $prices;

    private string $type = 'hourly';

    public function __construct(
        public string $band
    ) {
        $this->initalise($band);
    }

    public function price($type = 'hourly')
    {
        return $this->prices[$type] ?? $this->prices['hourly'];
    }

    private function initalise(string $band)
    {
        $this->prices = config('brandx.prices.' . $band);
    }
}
