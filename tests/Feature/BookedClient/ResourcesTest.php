<?php

namespace Tests\Feature\BookedClient;

use App\Booked\Models\BookedResource;
use App\Booked\Models\BookedResourceStatus;
use App\Booked\Models\BookedResourceStatusReason;
use App\Booked\Repositories\ResourceRepository;
use App\Exceptions\BookedModelNotFoundException;
use Illuminate\Support\Collection;
use Tests\Feature\FeatureTestCase;

class ResourcesTest extends FeatureTestCase
{
    protected ResourceRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();

        $this->repository = app(ResourceRepository::class);
    }

    /** @test */
    public function it_can_get_all_resources()
    {
        $resourceCollection = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $resourceCollection);

        $resourceCollection->each(
            fn (BookedResource $resource) => $this->assertInstanceOf(BookedResource::class, $resource)
        );
    }

    /** @test */
    public function it_can_get_a_single_resource()
    {
        $resource = $this->repository->all()->random();

        $resource = $this->repository->find($resource->resourceId);

        $this->assertInstanceOf(BookedResource::class, $resource);
    }

    /** @test */
    public function it_throws_exception_if_resource_not_found()
    {
        $this->expectException(BookedModelNotFoundException::class);

        $this->repository->find(999999999);
    }

    /** @test */
    public function it_can_get_all_statuses()
    {
        $statusCollection = $this->repository->statuses();

        $this->assertInstanceOf(Collection::class, $statusCollection);

        $statusCollection->each(
            fn (BookedResourceStatus $status) => $this->assertInstanceOf(BookedResourceStatus::class, $status)
        );
    }

    /** @test */
    public function it_can_get_all_status_reasons()
    {
        $reasonCollection = $this->repository->statusReasons();

        $this->assertInstanceOf(Collection::class, $reasonCollection);

        $reasonCollection->each(
            fn (BookedResourceStatusReason $reason) => $this->assertInstanceOf(
                BookedResourceStatusReason::class,
                $reason
            )
        );
    }
}
