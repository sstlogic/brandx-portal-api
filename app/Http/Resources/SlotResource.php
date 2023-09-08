<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

/**
 * @mixin \App\Booked\Models\BookedSlot
 */
class SlotResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            ...parent::toArray($request),
            'uuid' => Str::uuid(),
            'date' => $this->date->format('Y-m-d'),
            'duration' => $this->getDurationInMinutes(),
            'resource' => $this->when(
                $this->resource->resource,
                fn () => new ResourceResource($this->resource->resource)
            ),
        ];
    }
}
