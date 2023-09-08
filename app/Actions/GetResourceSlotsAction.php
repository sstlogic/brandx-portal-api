<?php

namespace App\Actions;

use App\Booked\Collections\SlotCollection;
use App\Booked\Models\BookedResource;
use App\Booked\Repositories\SlotRepository;

class GetResourceSlotsAction extends BaseAction
{
    private BookedResource $resource;

    public function __construct(
        private SlotRepository $repository
    ) {}

    public function execute(BookedResource $resource, array $data): SlotCollection
    {
        $this->resource = $resource;

        $this->setData($data);

        return $this->getSlots();
    }

    public function getSlots(): SlotCollection
    {
        $start = $this->getDateFromData('start_time');
        $end = $this->getDateFromData('end_time');

        return $this->repository
            ->forResource(
                $this->resource,
                $start,
                $end
            );
    }
}
