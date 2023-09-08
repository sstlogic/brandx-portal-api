<?php

namespace App\Booked\Client\Responses;

use App\Booked\Collections\SlotCollection;
use App\Booked\Models\BookedResource;
use App\Booked\Models\BookedSlot;
use Illuminate\Support\Collection;

class GetScheduleSlotsResponse
{
    public function __construct(public array $raw) {}

    public function slots(): SlotCollection
    {
        return SlotCollection::make($this->getAllSlots());
    }

    private function getAllSlots(): Collection
    {
        $foo = $this->collectRaw('dates')
            ->map(function (array $data) {
                return $this->getSlotsForDate($data);
            })->flatten()
            ->filter(function (BookedSlot $slot) {
                return $slot->resource->isAvailable();
            });

        return $foo;
    }

    private function collectRaw(?string $key = null): Collection
    {
        return collect($key ? $this->raw[$key] : $this->raw);
    }

    private function getSlotsForDate(array $data): SlotCollection
    {
        $date = $data['date'];

        $resources = $data['resources'];

        $slots = collect($resources)
            ->map(
                fn (array $resource) => $this->getResourceSlots($resource, $date)
            )
            ->flatten();

        return new SlotCollection($slots);
    }

    private function getResourceSlots(array $resource, string $date): Collection
    {
        $slots = collect($resource['slots']);

        return $slots->map(fn (array $slot) => $this->createSlot($slot, $resource, $date));
    }

    private function createSlot(array $slot, array $resource, string $date): BookedSlot
    {
        $resource = new BookedResource([
            'name' => $resource['resourceName'],
            'resourceId' => $resource['resourceId'],
        ]);

        return BookedSlot::make($slot)
            ->setDate($date)
            ->setResource($resource);
    }
}
