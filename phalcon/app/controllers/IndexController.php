<?php

use Phalcon\Mvc\Controller;

class IndexController extends Controller
{
    public function indexAction()
    {
        echo "<h1>Hello World</h1><h2>PHP: " . PHP_VERSION . "</h2><h2>Phalcon: " . \Phalcon\Version::get() . "</h2>";
    }
}
