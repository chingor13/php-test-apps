<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\ClientInterface;

use Google\Cloud\Trace\Integrations\Guzzle\Middleware;

class NestedController extends Controller
{

    public function parent(ClientInterface $client)
    {
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
