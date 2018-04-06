<?php

require_once __DIR__ . '/../vendor/autoload.php';

use OpenCensus\Trace\Tracer;
use OpenCensus\Trace\Exporter\EchoExporter;

var_dump($_SERVER);
$exporter = new EchoExporter();
$tracer = Tracer::start($exporter, [
    // 'startTime' => microtime(true)
]);
var_dump($tracer->tracer());
