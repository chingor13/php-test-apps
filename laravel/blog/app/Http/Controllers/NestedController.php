<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\Trace\RequestTracer;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;

use Google\Cloud\Trace\Integrations\Guzzle\TraceContextMiddleware;

class NestedController extends Controller
{

    public function parent()
    {
        // create a guzzle client
        $stack = new HandlerStack();
        $stack->setHandler(\GuzzleHttp\choose_handler());

        $stack->push(new TraceContextMiddleware());
        $client = new Client(['handler' => $stack]);

        // $url = 'https://' . $_SERVER['HTTP_HOST'] . '/nested/child';
        $url = 'https://requestb.in/1dlrmon1';
        // var_dump($url);
        $client->get($url);

        return response()->json([
            'action' => 'parent',
            'url' => $url
        ]);
    }

    public function child()
    {
        return response()->json([
            'action' => 'child'
        ]);
    }
}
