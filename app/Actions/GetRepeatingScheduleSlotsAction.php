<?php

namespace App\Actions;

use App\Booked\Collections\ResourceCollection;
use App\Booked\Collections\SlotCollection;
use App\Booked\Models\BookedSchedule;
use App\Booked\Repositories\SlotRepository;
use Carbon\Carbon;
use Carbon\CarbonInterface;
use Carbon\CarbonPeriod;
use Illuminate\Support\Collection;

class GetRepeatingScheduleSlotsAction extends BaseAction
{
    private BookedSchedule $schedule;

    public function __construct(
        private SlotRepository $repository
    ) {}

    public function execute(array $data, BookedSchedule $schedule): ResourceCollection
    {
        $this->schedule = $schedule;

        $this->setData($data);

        return $this->getSlots()->groupByResource();
    }

    public function dates(array $data)
    {
        $this->setData($data);

        return $this->getDates();
    }

    private function getSlots(): SlotCollection
    {
        return $this->repository
            ->forSchedule(
                $this->schedule,
                $this->getStartDate(),
                $this->getEndDate(),
            )->onDates($this->getDates());
    }

    private function getWeeklyDays(): array
    {
        return $this->data['weekly_days'];
    }

    private function getStartDate(): Carbon
    {
        return $this->getDateFromData('start_date')->setTimezone('Australia/Sydney');
    }

    private function getEndDate(): Carbon|CarbonInterface
    {
        return $this->getDateFromData('end_date')->setTimezone('Australia/Sydney');
    }

    private function getPeriod(): CarbonPeriod
    {
        return CarbonPeriod::create(
            $this->getStartDate(),
            $this->data['interval'] . ' ' . $this->getIntervalTypeKey(),
            $this->getEndDate()
        );
    }

    private function getIntervalTypeKey(): string
    {
        $types = [
            'weekly' => 'week',
            'daily' => 'day',
            'monthly-day' => 'month',
            'monthly-date' => 'month',
        ];

        return $types[$this->data['interval_type']];
    }

    private function getDates()
    {
        return match ($this->data['interval_type']) {
            'weekly' => $this->weeklyDates(),
            'daily' => $this->dailyDates(),
            'monthly-day' => $this->monthlyDatesOnTheDay(),
            'monthly-date' => $this->monthlyDatesOnTheDate()
        };
    }

    private function dailyDates(): Collection
    {
        return collect($this->getPeriod());
    }

    private function weeklyDates(): Collection
    {
        $days = $this->getWeeklyDays();

        $dates = collect($this->getPeriod())
            ->map(fn (Carbon $date) => $this->getDaysOfWeek($date, $days))
            ->flatten();

        if (! $dates->contains(function (Carbon $date) {
            return $date->isSameDay($this->getStartDate());
        })) {
            $dates->push($this->getStartDate());
        }

        return $dates;
    }

    private function monthlyDatesOnTheDate(): Collection
    {
        return collect($this->getPeriod());
    }

    private function monthlyDatesOnTheDay(): Collection
    {
        $start = $this->getStartDate();

        $startOfMonthDayOfWeek = $start->copy()->firstOfMonth()->dayOfWeekIso;

        $nowDayOfWeek = $start->dayOfWeekIso;
        $nowWeek = $start->weekNumberInMonth;

        $nth = $nowDayOfWeek < $startOfMonthDayOfWeek
            ? $nowWeek - 1
            : $nowWeek;

        return collect($this->getPeriod())
            ->map(fn (Carbon $date) => $date->nthOfMonth($nth, $start->dayOfWeek))
            ->reject(fn (Carbon $date) => ! $date);
    }

    private function getDaysOfWeek(Carbon $date, array $days): Collection
    {
        return collect($days)
            ->map(fn (int $day) => $date->copy()->startOf('week')->addDays($day))
            ->reject(fn (Carbon $date) => $date->isBefore($this->getStartDate()))
            ->reject(fn (Carbon $date) => $date->isAfter($this->getEndDate()));
    }
}
