<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Google\Cloud\Trace\RequestTracer;
use Google\Cloud\Trace\Reporter\AsyncReporter;

$reporter = new AsyncReporter([]);
RequestTracer::start($reporter);

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views',
));

$app->get('/', function() {
    return 'Hello from Silex ' . Silex\Application::VERSION;
});

$app->get('/hello/{name}', function ($name) use ($app) {
    return $app['twig']->render('hello.html.twig', [
        'name' => $name
    ]);
});

$app->get('/env', function() use ($app) {
    return $app->json($_SERVER, 200, ['Content-Type' => 'application/json']);
});

$app->get('/metadata/{key}', function($key) use ($app) {
    $uri = 'http://metadata.google.internal/computeMetadata/v1/' . $key;

    $client = new \GuzzleHttp\Client();
    $resp = $client->get($uri, [
        'headers' => [
            'Metadata-Flavor' => 'Google'
        ],
        'query' => [
            'recursive' => 'true'
        ]
    ]);
    return $resp->getBody();
})->value('key', '');

RequestTracer::inSpan(['name' => 'run'], [$app, 'run']);
//$app->run();
