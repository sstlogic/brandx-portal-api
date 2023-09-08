<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin \App\Booked\Models\BookedSchedule
 */
class ScheduleResource extends JsonResource
{
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'resources' => $this->when(
                $this->resources->isNotEmpty(),
                ResourceResource::collection($this->resources),
                []
            ),
            'isDefault' => $this->isDefault(),
        ];
    }
}
