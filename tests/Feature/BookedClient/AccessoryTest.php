<?php

namespace Tests\Feature\BookedClient;

use App\Booked\Models\BookedAccessory;
use App\Booked\Repositories\AccessoryRepository;
use App\Exceptions\BookedModelNotFoundException;
use Illuminate\Support\Collection;
use Tests\Feature\FeatureTestCase;

class AccessoryTest extends FeatureTestCase
{
    protected AccessoryRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = app(AccessoryRepository::class);
    }

    /** @test */
    public function it_can_get_all_accessories()
    {
        $accessoryCollection = $this->repository->all();

        $this->assertInstanceOf(Collection::class, $accessoryCollection);

        $accessoryCollection->each(
            fn (BookedAccessory $accessory) => $this->assertInstanceOf(BookedAccessory::class, $accessory)
        );
    }

    /** @test */
    public function it_can_get_a_single_accessory()
    {
        /** @var BookedAccessory $acc */
        $acc = $this->repository->all()->random();

        $acc = $this->repository->find($acc->id);

        $this->assertInstanceOf(BookedAccessory::class, $acc);
    }

    /** @test */
    public function it_throws_exception_if_accessory_not_found()
    {
        $this->expectException(BookedModelNotFoundException::class);

        $acc = $this->repository->find(99999999);
    }
}
