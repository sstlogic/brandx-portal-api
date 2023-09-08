<?php

namespace Tests\Feature;

use App\Booked\Client\BookedClient;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

class FeatureTestCase extends TestCase
{
    use RefreshDatabase;
    
    protected function getBadClient()
    {
        App::forgetInstance(BookedClient::class);

        Config::set('booked.api_password', 'bad_password');

        return app(BookedClient::class);
    }
}
