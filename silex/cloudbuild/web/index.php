<?php

require_once __DIR__ . '/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Response;

$app = new Silex\Application();

$app->register(new Silex\Provider\TwigServiceProvider(), array(
    'twig.path' => __DIR__ . '/../views'
));

$app->get('/', function() use ($app) {
    return $app['twig']->render('index.html.twig');
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
    $response = new Response($resp->getBody());
    $response->headers->set('Content-Type', 'application/json');
    return $response;
})->value('key', '');

$app->run();
