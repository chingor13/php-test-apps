<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\TraceClient;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\EchoReporter;
use Google\Cloud\Trace\Reporter\TraceReporter;
use Psr\Cache\CacheItemPoolInterface;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItem;

class GoogleCloudProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(TraceClient $trace, CacheItemPoolInterface $cache)
    {
        // don't trace if we're running in the console (i.e. a php artisan command)
        if (php_sapi_name() == 'cli') {
            return;
        }

        $reporter = new TraceReporter($trace);
        RequestTracer::start($trace, $reporter, [
            'qps' => [
                'cache' => $cache,
                'cacheItemClass' => CacheItem::class
            ],
            'startTime' => LARAVEL_START
        ]);
        RequestTracer::retroSpan(
            microtime(true) - LARAVEL_START,
            [
                'name' => 'bootstrap'
            ]
        );

        \Event::listen('Illuminate\Database\Events\QueryExecuted', function($event) {
            RequestTracer::retroSpan(
                $event->time * 0.001,
                [
                    'name' => $event->connectionName,
                    'labels' => [
                        'query' => $event->sql
                    ]
                ]
            );
        });
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton(ServiceBuilder::class, function($app) {
            return new ServiceBuilder($app['config']['services']['google']);
        });
        $this->app->singleton(TraceClient::class, function($app) {
            return $app->make(ServiceBuilder::class)->trace();
        });
    }

    public function provides()
    {
        return [
            ServiceBuilder::class,
            TraceClient::class
        ];
    }
}
