<?php

$when = microtime(true);
$micro = sprintf("%06d", ($when - floor($when)) * 1000000);
$when = new \DateTime(date('Y-m-d H:i:s.'. $micro, (int) $when));
var_dump($when, $when->format('U.u'));