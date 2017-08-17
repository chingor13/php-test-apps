<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Google\Auth\HttpHandler\HttpHandlerFactory;
use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\TraceClient;

use OpenCensus\Trace\RequestTracer;
use OpenCensus\Trace\Reporter\ReporterInterface;
use OpenCensus\Trace\Reporter\ZipkinReporter;
use OpenCensus\Trace\Reporter\GoogleCloudReporter;
use OpenCensus\Trace\Reporter\EchoReporter;
use OpenCensus\Trace\Sampler\SamplerInterface;
use OpenCensus\Trace\Sampler\AlwaysOnSampler;
use OpenCensus\Trace\Sampler\QpsSampler;

use OpenCensus\Trace\Integrations\Curl;
use OpenCensus\Trace\Integrations\Laravel;
use OpenCensus\Trace\Integrations\Mysql;
use OpenCensus\Trace\Integrations\PDO;
use OpenCensus\Trace\Integrations\Guzzle;
use OpenCensus\Trace\Integrations\Grpc;
use OpenCensus\Trace\Integrations\Guzzle\Middleware;

use Google\Cloud\Translate\TranslateClient;
use Google\Cloud\PubSub\PubSubClient;
use GuzzleHttp\ClientInterface as GuzzleClient;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\CacheItemInterface;
use Illuminate\Database\Events\QueryExecuted;
use App\GoogleCloudConfig;
use Google\Cloud\Core\Batch\BatchRunner;

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

        Laravel::load();
        Mysql::load();
        PDO::load();
        Curl::load();
        Grpc::load();

        opencensus_trace_method(BatchRunner::class, 'submitItem', function ($batchRunner, $identifier, $item) {
            return [
                'labels' => [
                    'identifier' => $identifier
                ]
            ];
        });

        // start the root span
        RequestTracer::start($reporter, [
            'sampler' => $sampler
        ]);

        // create a span from the initial start time until now as 'bootstrap'
        RequestTracer::startSpan(['name' => 'bootstrap', 'startTime' => LARAVEL_START]);
        RequestTracer::endSpan();
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        // $this->app->bind(Middleware::class, function () {
        //     return new Middleware();
        // });
        // $this->app->tag([
        //     Middleware::class
        // ], 'guzzle.middleware');

        $this->app->singleton(ServiceBuilder::class, function($app) {
            $config = new GoogleCloudConfig();
            $config = $app['config']['services']['google'] + $config->getConfigAsArray();
            return new ServiceBuilder($config);
        });
        $this->app->singleton(TraceClient::class, function($app) {
            return $app->make(ServiceBuilder::class)->trace();
        });
        $this->app->singleton(TranslateClient::class, function($app) {
            return $app->make(ServiceBuilder::class)->translate();
        });
        $this->app->singleton(ReporterInterface::class, function($app) {
            $config = new GoogleCloudConfig();
            $config = $app['config']['services']['google'];// + $config->getConfigAsArray();
            return new GoogleCloudReporter(['clientConfig' => $config, 'async' => true]);
        });
        $this->app->singleton(SamplerInterface::class, function($app) {
            return new AlwaysOnSampler();
            // return new QpsSampler(
            //     $app->make(CacheItemPoolInterface::class),
            //     ['cacheItemClass' => get_class($app->make(CacheItemInterface::class))]
            // );
        });
        $this->app->singleton(PubSubClient::class, function($app) {
            return new PubSubClient([
                'transport' => 'grpc'
            ]);
        });
    }

    public function provides()
    {
        return [
            ServiceBuilder::class,
            TraceClient::class,
            ReporterInterface::class,
            SamplerInterface::class,
            TranslateClient::class,
            PubSubClient::class
        ];
    }
}
