<?php

namespace App\Booked\Models;

use App\Booked\Collections\ResourceCollection;

/**
 * @property string|int $id
 * @property string $name
 * @property int $isDefault;
 */
class BookedSchedule extends BookedModel
{
    public ?ResourceCollection $resources;

    public function __construct(array $attributes)
    {
        parent::__construct($attributes);
        $this->resources = new ResourceCollection();
    }

    public function isDefault(): bool
    {
        return (bool) $this->isDefault;
    }
}
