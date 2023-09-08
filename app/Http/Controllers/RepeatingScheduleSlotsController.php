<?php

namespace App\Http\Controllers;

use App\Actions\GetRepeatingScheduleSlotsAction;
use App\Booked\Models\BookedSchedule;
use App\Booked\Repositories\ScheduleRepository;
use App\Http\Requests\RepeatingScheduleSlotQueryRequest;
use App\Http\Resources\ScheduleResource;

class RepeatingScheduleSlotsController extends Controller
{
    public function __invoke(
        RepeatingScheduleSlotQueryRequest $request,
        GetRepeatingScheduleSlotsAction   $action,
        ScheduleRepository                $repository,
        int                               $schedule
    ) {
        $schedule = $repository->find($schedule);

        $resources = $action->execute($request->validated(), $schedule);

        $schedule = BookedSchedule::make([
            'id' => $schedule,
        ]);

        $schedule->resources = $resources;

        return ScheduleResource::make($schedule);
    }
}
