<?php

namespace Tests\Feature;

use App\Booked\Client\BookedClient;
use App\Facades\Booked;

class Test extends FeatureTestCase
{
    /**
     * @test
     */
    public function facade_returns_client_class()
    {
        $instance = Booked::getInstance();

        $this->assertInstanceOf(BookedClient::class, $instance);
    }

    /**
     * @test
     */
    public function it_resolves_the_same_instance()
    {
        $this->assertSame(app(BookedClient::class), app(BookedClient::class));
    }
}
