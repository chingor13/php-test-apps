<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use GuzzleHttp\Client;
use GuzzleHttp\ClientInterface;
use GuzzleHttp\HandlerStack;

class GuzzleProvider extends ServiceProvider
{
    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(ClientInterface::class, function ($app) {
            $stack = new HandlerStack();
            $stack->setHandler(\GuzzleHttp\choose_handler());

            foreach ($this->app->tagged('guzzle.middleware') as $middleware) {
                $stack->push($middleware);
            }

            return new Client(['handler' => $stack]);
        });
    }

    public function provides()
    {
        return [
            ClientInterface::class
        ];
    }
}
