<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\TraceClient;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\EchoReporter;
use Google\Cloud\Trace\Reporter\FileReporter;
use Google\Cloud\Trace\Reporter\SyncReporter;
use Google\Cloud\Trace\Reporter\ReporterInterface;
use Google\Cloud\Trace\Sampler\SamplerInterface;
use Google\Cloud\Trace\Sampler\QpsSampler;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use Illuminate\Database\Events\QueryExecuted;

class GoogleCloudProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot(ReporterInterface $reporter, SamplerInterface $sampler)
    {
        // don't trace if we're running in the console (i.e. a php artisan command)
        if (php_sapi_name() == 'cli') {
            return;
        }

        // start the root span
        RequestTracer::start($reporter, [
            'sampler' => $sampler
        ]);

        // create a span from the initial start time until now as 'bootstrap'
        RequestTracer::startSpan(['name' => 'bootstrap', 'startTime' => LARAVEL_START]);
        RequestTracer::endSpan();

        // For every Eloquent query execute, create a span with the query as a label
        \Event::listen(QueryExecuted::class, function(QueryExecuted $event) {
            $startTime = microtime(true) - $event->time * 0.001;
            RequestTracer::startSpan([
                'name' => $event->connectionName,
                'labels' => [
                    'query' => $event->sql
                ],
                'startTime' => $startTime
            ]);
            RequestTracer::endSpan();
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
            // return new FileReporter("/tmp/spans.log");
            // return new EchoReporter();
            return new SyncReporter($app->make(TraceClient::class));
        });
        $this->app->singleton(SamplerInterface::class, function($app) {
            return new QpsSampler(
                $app->make(CacheItemPoolInterface::class),
                get_class($app->make(CacheItemInterface::class))
            );
        });
    }

    public function provides()
    {
        return [
            ServiceBuilder::class,
            TraceClient::class,
            ReporterInterface::class,
            SamplerInterface::class
        ];
    }
}
