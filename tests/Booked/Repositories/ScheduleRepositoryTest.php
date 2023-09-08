<?php

namespace Tests\Booked\Repositories;

use App\Booked\Models\BookedSchedule;
use App\Booked\Repositories\ScheduleRepository;
use Illuminate\Support\Collection;
use Tests\Feature\FeatureTestCase;

class ScheduleRepositoryTest extends FeatureTestCase
{
    protected ScheduleRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(ScheduleRepository::class);
    }

    /** @test */
    public function it_can_get_all_schedules()
    {
        $scheduleCollection = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $scheduleCollection);

        $scheduleCollection->each(
            fn (BookedSchedule $schedule) => $this->assertInstanceOf(BookedSchedule::class, $schedule)
        );
    }

    /** @test */
    public function it_can_get_a_single_schedule()
    {
        /** @var BookedSchedule $schedule */
        $schedule = $this->repository->all()->random();

        $this->repository->find($schedule->id);

        $this->assertInstanceOf(BookedSchedule::class, $schedule);
    }
}

