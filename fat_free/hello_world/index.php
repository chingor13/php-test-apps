<?php

require('./vendor/autoload.php');

$f3 = \Base::instance();
$f3->route('GET /hello/@name',
    function($f3) {
        echo 'Hello ' . $f3->get('PARAMS.name');
    }
);
$f3->run();
