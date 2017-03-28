<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\TraceClient;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\EchoReporter;
use Google\Cloud\Trace\Reporter\TraceReporter;
use Google\Cloud\Trace\Reporter\ReporterInterface;
use Psr\Cache\CacheItemPoolInterface;
use Madewithlove\IlluminatePsrCacheBridge\Laravel\CacheItem;
use Illuminate\Database\Events\QueryExecuted;

class GoogleCloudProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ReporterInterface $reporter, CacheItemPoolInterface $cache)
    {
        // don't trace if we're running in the console (i.e. a php artisan command)
        if (php_sapi_name() == 'cli') {
            return;
        }

        // start the root span
        RequestTracer::start($reporter, [
            'qps' => [
                'cache' => $cache,
                'cacheItemClass' => CacheItem::class
            ],
            'startTime' => LARAVEL_START
        ]);

        // create a span from the initial start time until now as 'bootstrap'
        RequestTracer::startSpan(['name' => 'bootstrap', 'startTime' => LARAVEL_START]);
        RequestTracer::finishSpan();

        // For every Eloquent query execute, create a span with the query as a label
        \Event::listen(QueryExecuted::class, function(QueryExecuted $event) {
            $startTime = microtime(true) - $event->time * 0.001;
            RequestTracer::startSpan([
                'name' => $event->connectionName,
                'labels' => [
                    'query' => $event->sql
                ],
                'startTime' => $startTime]
            );
            RequestTracer::finishSpan();
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
        $this->app->singleton(ReporterInterface::class, function($app) {
            return new TraceReporter($app->make(TraceClient::class));
        });
    }

    public function provides()
    {
        return [
            ServiceBuilder::class,
            TraceClient::class,
            TraceReporterInterface::class
        ];
    }
}
