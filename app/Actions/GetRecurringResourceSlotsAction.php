<?php

namespace App\Actions;

use App\Booked\Collections\SlotCollection;
use App\Booked\Models\BookedResource;
use App\Booked\Repositories\SlotRepository;

class GetRecurringResourceSlotsAction extends BaseAction
{
    private BookedResource $resource;

    private int $intervalCount;

    public function __construct(
        private SlotRepository $repository
    ) {}

    public function execute(BookedResource $resource, array $data): SlotCollection
    {
        $this->resource = $resource;

        $this->setData($data);

        $this->intervalCount = $this->data['interval_count'] ?? 6;

        return $this->getSlotsForInterval();
    }

    private function getSlotsForInterval()
    {
        $interval = $this->data['interval'];

        return match ($interval) {
            'weekly' => $this->handleWeeklyIntervals(),
            'monthly_date' => $this->handleMonthlyDateIntervals(),
            'monthly_day' => $this->handleMonthlyDayIntervals(),
            'daily' => $this->handleDailyIntervals(),
            default => $this->handleInvalidInterval()
        };
    }

    private function handleDailyIntervals()
    {
        $start = $this->getDateFromData('start_time');
        $end = $start->copy()->addDays($this->intervalCount);

        $slotStart = $this->getDateFromData('slot_start');

        $slots = $this->repository
            ->forResource(
                $this->resource,
                $start,
                $end
            );

        return $slots;
    }

    private function handleWeeklyIntervals()
    {
        $start = $this->getDateFromData('start_time');
        $end = $start->copy()->addWeeks($this->intervalCount);

        $slotStart = $this->getDateFromData('slot_start');

        $slots = $this->repository
            ->forResource(
                $this->resource,
                $start,
                $end
            )->weeklyOn($slotStart, $this->intervalCount);

        return $slots;
    }

    private function handleMonthlyDateIntervals() {}

    private function handleMonthlyDayIntervals() {}

    private function handleInvalidInterval() {}
}
