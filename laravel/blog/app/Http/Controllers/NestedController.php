<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Trace\RequestTracer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use Google\Cloud\Trace\Integrations\Guzzle\Middleware;

class NestedController extends Controller
{

    public function parent()
    {
        // create a guzzle client
        $stack = new HandlerStack();
        $stack->setHandler(\GuzzleHttp\choose_handler());

        $stack->push(new Middleware());
        $client = new Client(['handler' => $stack]);

        $url = 'https://' . $_SERVER['HTTP_HOST'] . '/nested/child';
        $resp = $client->get($url);

        return response()->json([
            'action' => 'parent',
            'url' => $url,
            'code' => $resp->getHeaders()
        ]);
    }

    public function child()
    {
        return response()->json([
            'action' => 'child'
        ]);
    }
}
