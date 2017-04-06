<?php

namespace Google\Symfony;

use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\SyncReporter;
use Google\Cloud\Trace\Sampler\QpsSampler;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GoogleBundle extends Bundle
{

    public function boot()
    {
        // don't trace if we're running in the console (i.e. a php artisan command)
        if (php_sapi_name() == 'cli') {
            return;
        }

        if (!defined('SYMFONY_START')) {
            trigger_error('Please define \'SYMFONY_START\' in your `autoload.php`', E_USER_WARNING);
            define('SYMFONY_START', microtime(true));
        }
        $builder = new ServiceBuilder(['projectId' => 'chingor-php-gcs']);
        $trace = $builder->trace();
        $reporter = new SyncReporter($trace);

        RequestTracer::start($reporter);

        // create a span from the initial start time until now as 'bootstrap'
        RequestTracer::startSpan(['name' => 'bootstrap', 'startTime' => SYMFONY_START]);
        RequestTracer::finishSpan();

        $doctrine = $this->container->get('doctrine');
        $em = $doctrine->getConnection();
        $stack = new QueryLogger();
        $em->getConfiguration()->setSQLLogger($stack);
    }
}