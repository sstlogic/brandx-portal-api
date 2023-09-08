<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Booked\Models\BookedResource
 */
class ResourceResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => (int) $this->resourceId,
            'scheduleId' => (int) $this->scheduleId,
            'name' => $this->name,
            'slots' => $this->when($this->slots?->isNotEmpty(), fn () => SlotResource::collection($this->slots)),
            'description' => $this->description,
            'status' => [
                'id' => $this->status?->id,
                'name' => $this->status?->name,
            ],
            'capacity' => $this->maxParticipants,
            'location' => $this->location,
            'notes' => $this->notes,
        ];
    }
}
