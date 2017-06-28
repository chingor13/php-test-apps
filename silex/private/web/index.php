<?php

require_once __DIR__ . '/../vendor/autoload.php';

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->get('/', function() {
    $c = new PrivatePackage\MyClass();
    $f = new PrivateGit\Foo();
    $f2 = new PrivateHg\Foo();
    $gl = new gitlab_private\MyClass();
    return $c->hello('guest') . $f->bar() . $f2->bar() . $gl->hello('gitlab');
});

$app->get('/hello/{name}', function ($name) use ($app) {
    return $app['twig']->render('hello.html.twig', [
        'name' => $name
    ]);
});

$app->run();
