<?php

namespace Tests\Booked\Models;

use App\Booked\Models\BookedResource;
use App\Booked\Repositories\ResourceRepository;
use Cache;
use Illuminate\Support\Collection;
use Tests\Feature\FeatureTestCase;

class BookedResourceTest extends FeatureTestCase
{
    /** @test */
    public function it_can_load_all_statuses()
    {
        $statuses = BookedResource::getAllStatuses();

        $this->assertInstanceOf(Collection::class, $statuses);
        $this->assertEquals([
            [
                'id' => 0,
                'name' => 'Hidden',
            ],
            [
                'id' => 1,
                'name' => 'Available',
            ],
            [
                'id' => 2,
                'name' => 'Unavailable',
            ],
        ], $statuses->toArray());
    }

    /** @test */
    public function it_caches_statuses()
    {
        BookedResource::getAllStatuses();

        $this->assertTrue(Cache::has('booked:resource:statuses'));
    }

    /** @test */
    public function it_returns_statuses_from_cache()
    {
        BookedResource::getAllStatuses();
        $spy = $this->spy(ResourceRepository::class);

        BookedResource::getAllStatuses();

        $spy->shouldNotHaveReceived('statuses');
    }
}

