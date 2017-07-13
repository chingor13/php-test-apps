<?php

namespace Google\Symfony;

use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\SyncReporter;
use Google\Cloud\Trace\Reporter\EchoReporter;
use Google\Cloud\Trace\Sampler\QpsSampler;
use Google\Cloud\Trace\Integrations\Mysql;
use Google\Cloud\Trace\Integrations\PDO;
use Google\Cloud\Trace\Integrations\Symfony;
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

        Symfony::load();
        PDO::load();
        Mysql::load();

        if (!defined('SYMFONY_START')) {
            trigger_error('Please define \'SYMFONY_START\' in your `autoload.php`', E_USER_WARNING);
            define('SYMFONY_START', microtime(true));
        }
        $builder = new ServiceBuilder(['projectId' => 'chingor-php-gcs']);
        $trace = $builder->trace();
        $reporter = new SyncReporter($trace);
        // $reporter = new EchoReporter();

        RequestTracer::start($reporter);

        // create a span from the initial start time until now as 'bootstrap'
        RequestTracer::startSpan(['name' => 'bootstrap', 'startTime' => SYMFONY_START]);
        RequestTracer::endSpan();

        // track all sql queries as spans
        $doctrine = $this->container->get('doctrine');
        $em = $doctrine->getConnection();
        $stack = new QueryLogger();
        $em->getConfiguration()->setSQLLogger($stack);
    }
}
