<?php

namespace Tests\Feature\BookedClient;

use App\Booked\Client\BookedClient;
use App\Booked\Models\LoginResponse;
use App\Exceptions\InvalidCredentialsException;
use Cache;
use Tests\Feature\FeatureTestCase;

class AuthenticationTest extends FeatureTestCase
{
    private BookedClient $client;

    protected function setUp(): void
    {
        parent::setUp();

        $this->client = app(BookedClient::class);
    }

    /** @test */
    public function it_can_attempt_login()
    {
        $response = $this->client->attemptLogin();

        $this->assertInstanceOf(LoginResponse::class, $response);
    }

    /** @test */
    public function it_can_attempt_login_successfully()
    {
        $response = $this->client->attemptLogin();

        $this->assertTrue($response->isLoggedIn());
        $this->assertNotNull($response->sessionToken);
        $this->assertNotNull($response->userId);
        $this->assertTrue($response->isAuthenticated);
    }

    /** @test */
    public function it_can_attempt_login_unsuccessfully()
    {
        $badClient = $this->getBadClient();

        $response = $badClient->attemptLogin();

        $this->assertFalse($response->isLoggedIn());
        $this->assertNull($response->sessionToken);
        $this->assertNull($response->userId);
        $this->assertFalse($response->isAuthenticated);
    }

    /** @test */
    public function it_stores_credentials_if_login_successful()
    {
        $this->client->authenticate();

        $this->assertNotNull(Cache::get("booked:session_token"));
        $this->assertNotNull(Cache::get("booked:user_id"));
    }

    /** @test */
    public function it_throws_exception_if_login_unsuccessful()
    {
        $badClient = $this->getBadClient();

        $this->expectException(InvalidCredentialsException::class);

        $badClient->authenticate();
    }

    /** @test */
    public function it_does_not_store_credentials_if_login_unsuccessful()
    {
        $badClient = $this->getBadClient();

        try {
            $badClient->authenticate();
        } catch (InvalidCredentialsException $e) {
            $this->assertNull(Cache::get("booked:session_token"));
            $this->assertNull(Cache::get("booked:user_id"));
        }
    }

    /** @test */
    public function it_logs_out_successful_if_logged_in()
    {
        $this->client->authenticate();

        $response = $this->client->logout();

        $this->assertTrue($response);
    }

    /** @test */
    public function it_returns_true_if_log_out_when_not_logged_in()
    {
        $response = $this->client->logout();

        $this->assertTrue($response);
    }
}
