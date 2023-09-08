<?php

namespace App\Booked\Models;

use Carbon\Carbon;
use Carbon\CarbonPeriod;

/**
 * @property \Carbon\Carbon $date
 * @property \Carbon\Carbon $startDateTime
 * @property \Carbon\Carbon $endDateTime
 */
class BookedSlot extends BookedModel
{
    public ?BookedReservation $reservation = null;

    public ?BookedResource $resource = null;

    protected array $dates = [
        'startDateTime',
        'endDateTime',
        'date',
    ];

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);

        if ($reservation = $attributes['reservation']) {
            $this->setReservation($reservation);
        }
    }

    public function setResource(BookedResource $resource): static
    {
        $this->resource = $resource;

        return $this;
    }

    public function setReservation(array $reservation): static
    {
        $this->reservation = new BookedReservation($reservation);

        return $this;
    }

    public function setDate(string $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function getDurationInMinutes(): int
    {
        if (Carbon::parse('2022-04-03T00:00:00+11:00')->isSameHour($this->startDateTime)) {
            return $this->startDateTime->diffInMinutes($this->endDateTime) - 60;
        }

        return $this->startDateTime->diffInMinutes($this->endDateTime);
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'reservation' => $this->reservation?->toArray(),
            'resource' => $this->resource?->toArray(),
        ];
    }

    public function isDuringTime(Carbon $start) {}

    public function startsOnOneOf(CarbonPeriod $period): bool
    {
        return collect($period)->contains(fn (Carbon $date) => $date->equalTo($this->startDateTime));
    }
}
