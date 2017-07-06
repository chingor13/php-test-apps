<?php

require_once __DIR__ . '/../vendor/autoload.php';
$agent = new Google\Cloud\Debugger\Agent(['debugOutput' => true, 'sourceRoot' => realpath('../../../')]);
