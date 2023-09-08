<?php

namespace App\Http\Controllers;

use App\Actions\GetRecurringResourceSlotsAction;
use App\Actions\GetResourceSlotsAction;
use App\Booked\Models\BookedResource;
use App\Booked\Repositories\ResourceRepository;
use App\Http\Requests\GetResourceSlotsRequest;
use App\Http\Resources\ResourceResource;
use App\Http\Resources\SlotResource;

class ResourceController extends Controller
{
    public function __construct(
        private ResourceRepository $repository
    ) {}

    public function index()
    {
        $resources = $this->repository->all();

        $resources = $resources->filter(fn (BookedResource $resource) => $resource->isAvailable());

        return ResourceResource::collection($resources);
    }

    public function show(int $resource)
    {
        $resource = $this->repository->find($resource);

        return ResourceResource::make($resource);
    }

    public function slots(GetResourceSlotsRequest $request, GetResourceSlotsAction $action, int $resource)
    {
        $resource = $this->repository->find($resource);

        $slots = $action->execute($resource, $request->validated());

        return SlotResource::collection($slots);
    }

    public function recurringSlots(
        GetResourceSlotsRequest         $request,
        GetRecurringResourceSlotsAction $action,
        int                             $resource
    ) {
        $resource = $this->repository->find($resource);

        $slots = $action->execute($resource, $request->validated());

        return SlotResource::collection($slots);
    }
}
