<?php

namespace App\Providers;

use App\Booked\Client\BookedClient;
use App\Booked\Client\BookedClientConfig;
use App\Booked\Client\BookedErrorHandler;
use Illuminate\Support\ServiceProvider;

class BookedServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->singleton(BookedClient::class, fn () => $this->resolveClient());
    }

    public function boot()
    {
        //
    }

    private function resolveClient(): BookedClient
    {
        return new BookedClient($this->getBookedClientConfig(), new BookedErrorHandler());
    }

    /**
     * @return \App\Booked\Client\BookedClientConfig
     */
    private function getBookedClientConfig(): BookedClientConfig
    {
        return new BookedClientConfig(
            config('booked.api_endpoint'),
            config('booked.api_username'),
            config('booked.api_password')
        );
    }
}
