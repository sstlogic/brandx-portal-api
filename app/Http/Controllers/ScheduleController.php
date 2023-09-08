<?php

namespace App\Http\Controllers;

use App\Booked\Collections\SlotCollection;
use App\Booked\Models\BookedSchedule;
use App\Booked\Repositories\ScheduleRepository;
use App\Booked\Repositories\SlotRepository;
use App\Http\Resources\ScheduleResource;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    public function __construct(
        private ScheduleRepository $scheduleRepository,
        private SlotRepository     $slotRepository
    ) {
    }

    public function index()
    {
        $schedules = $this->scheduleRepository->all();

        return ScheduleResource::collection($schedules);
    }

    public function show(int $schedule)
    {
        $schedule = $this->scheduleRepository->find($schedule);

        return ScheduleResource::make($schedule);
    }

    public function slots(Request $request, int $schedule)
    {
        $request->validate([
            'resource' => ['string', 'numeric', 'nullable', 'sometimes'],
        ]);

        $resources = $this
            ->slotRepository
            ->forSchedule(
                schedule: $schedule,
                start: $request->date('start_time'),
                end: $request->date('end_time'),
                resourceId: $request->input('resource')
            )
            ->when($request->input('repeating'), function (SlotCollection $collection) use ($request) {
                return $collection->repeating(
                    start: $request->date('start_time'),
                    intervalType: $request->input('interval_type'),
                    interval: $request->input('interval'),
                    until: $request->date('until'),
                    intervalCount: $request->input('interval_count')
                );
            })
            ->groupByResource();

        $schedule = BookedSchedule::make([
            'id' => $schedule,
        ]);

        $schedule->resources = $resources;

        return ScheduleResource::make($schedule);
    }
}
