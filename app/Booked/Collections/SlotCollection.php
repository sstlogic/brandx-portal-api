<?php

namespace App\Booked\Collections;

use App\Booked\Models\BookedResource;
use App\Booked\Models\BookedSlot;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class SlotCollection extends Collection
{
    public function __construct($items = [])
    {
        parent::__construct($items);
    }

    public function groupByResource(): ResourceCollection
    {
        $resources = $this->getResources();

        return $resources->map(
            fn (BookedResource $resource) => $resource->clone()->setSlots($this->forResource($resource))
        );
    }

    public function weeklyOn(Carbon $start, int $count)
    {
        $period = CarbonPeriod::create($start, '1 week', $count);

        return $this->filter(fn (BookedSlot $slot) => $slot->startsOnOneOf($period));
    }

    public function forTime(Carbon $start)
    {
        return $this->filter(fn (BookedSlot $slot) => $slot->isDuringTime($start));
    }

    public function onDates(Collection $dates)
    {
        return $this->filter(
            fn (BookedSlot $slot) => $dates->contains(function (Carbon $date) use ($slot) {
                return $date->isSameDay($slot->date);
            })
        );
    }

    public function repeating(
        Carbon     $start,
        string     $intervalType,
        string|int $interval,
        ?Carbon    $until,
        int        $intervalCount
    ): SlotCollection {
        $dates = $this->getIntervalDates(
            start: $start,
            intervalType: $intervalType,
            interval: $interval,
            until: $until,
            intervalCount: $intervalCount
        );

        return $this->filter(
            fn (BookedSlot $slot) => $dates->contains(function (Carbon $date) use ($slot) {
                return $date->isSameDay($slot->date);
            })
        );
    }

    private function getResources(): ResourceCollection
    {
        return ResourceCollection::make(
            $this->map(fn (BookedSlot $slot) => $slot->resource)
                ->unique(fn (BookedResource $resource) => $resource->resourceId)
        );
    }

    private function forResource(BookedResource $resource): SlotCollection
    {
        return $this->filter(fn (BookedSlot $slot) => $slot->resource?->resourceId === $resource->resourceId);
    }

    private function getIntervalDates(
        Carbon     $start,
        string     $intervalType,
        string|int $interval,
        ?Carbon    $until,
        int        $intervalCount
    ) {
        $types = [
            'weekly' => 'week',
            'monthly' => 'month',
        ];

        return collect(CarbonPeriod::create($start, "$interval $types[$intervalType]", $intervalCount));
    }
}
