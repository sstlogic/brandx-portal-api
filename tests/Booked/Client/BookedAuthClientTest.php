<?php

namespace Tests\Booked\Client;

use App\Booked\Client\BookedAuthClient;
use App\Booked\Models\BookedUser;
use Tests\Feature\FeatureTestCase;

class BookedAuthClientTest extends FeatureTestCase
{
    private BookedAuthClient $auth;

    protected function setUp(): void
    {
        parent::setUp();
        $this->auth = app(BookedAuthClient::class);
    }

    /** @test */
    public function it_can_login()
    {
        $user = $this->auth->login('api@lioneagle.solutions', 'password');

        $this->assertInstanceOf(BookedUser::class, $user);
        $this->assertNotNull($user->id);
    }

    /** @test */
    public function it_returns_null_if_invalid_credentials()
    {
        $user = $this->auth->login('foo', 'bar');

        $this->assertNull($user);
    }
}

