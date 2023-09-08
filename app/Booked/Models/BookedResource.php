<?php

namespace App\Booked\Models;

use App\Booked\Collections\SlotCollection;
use App\Booked\Repositories\ResourceRepository;
use Cache;
use Illuminate\Support\Collection;

/**
 * @property int $resourceId
 * @property string|int $statusId
 * @property string $name
 * @property ?string $description
 * @property int $scheduleId
 * @property string|int $maxParticipants
 * @property string $location
 * @property string $notes
 */
class BookedResource extends BookedModel
{
    public ?SlotCollection $slots;

    public ?BookedResourceStatus $status = null;

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
        $this->slots = new SlotCollection();
        $this->setStatus();
    }

    public function setSlots(?SlotCollection $slots): static
    {
        $this->slots = $slots;

        return $this;
    }

    public function isAvailable(): bool
    {
        if ($this->status) {
            return (int) $this->status->id === 1;
        }

        $stored = Cache::remember('resource:available', now()->addMinute(), function () {
            return self::getStoredResources()->first(function (BookedResource $resource) {
                return $resource->resourceId === $this->resourceId;
            });
        });

        return (int) $stored->status->id === 1;
    }

    public function toArray(): array
    {
        return [
            ...parent::toArray(),
            'slots' => $this->slots?->toArray(),
        ];
    }

    public static function getAllStatuses(): Collection
    {
        return Cache::remember('booked:resource:statuses', now()->addDay(), function () {
            return app(ResourceRepository::class)->statuses();
        });
    }

    public static function getStoredResources(): Collection
    {
        return Cache::remember('booked:resources:status', now()->addDay(), function () {
            return app(ResourceRepository::class)->all();
        });
    }

    private function setStatus()
    {
        $status = static::getAllStatuses();

        $this->status = $status
            ->filter(function (BookedResourceStatus $status) {
                if (is_null($this->statusId)) {
                    return false;
                }

                return $status->id === (int) $this->statusId;
            })
            ->first();
    }
}
