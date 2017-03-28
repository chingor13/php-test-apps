<?php

namespace Google\Symfony;

use Google\Cloud\ServiceBuilder;
use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\TraceReporter;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GoogleBundle extends Bundle
{

    public function boot()
    {
        if (!defined('SYMFONY_START')) {
            trigger_error('Please define \'SYMFONY_START\' in your `autoload.php`', E_USER_WARNING);
            define('SYMFONY_START', microtime(true));
        }
        $builder = new ServiceBuilder(['projectId' => 'chingor-php-gcs']);
        $trace = $builder->trace();
        $reporter = new TraceReporter($trace);
        RequestTracer::start($trace, $reporter, [
            'enabled' => true,
            'startTime' => SYMFONY_START
        ]);
        RequestTracer::retroSpan(
            microtime(true) - SYMFONY_START,
            ['name' => 'bootstrap']
        );

        $doctrine = $this->container->get('doctrine');
        $em = $doctrine->getConnection();
        $stack = new QueryLogger();
        $em->getConfiguration()->setSQLLogger($stack);
    }
}
