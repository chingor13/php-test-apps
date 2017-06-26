<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GoogleClient;
use GuzzleHttp\ClientInterface;

class GoogleApiClientProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(Google_Client::class, function($app) {
            $http = $app->make(ClientInterface::class);

            $client = new Google_Client();
            $client->useApplicationDefaultCredentials();
            $client->setHttpClient($http);
            return $client;
        });
    }

    public function provides()
    {
        return [
            Google_Client::class
        ];
    }
}
