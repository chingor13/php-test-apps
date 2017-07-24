<?php

namespace App;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Google\Cloud\Trace\Integrations\Guzzle\Middleware as TraceMiddleware;

class GoogleCloudConfig
{
    private static $client;

    /**
     * @param RequestInterface $request
     * @param array $options
     * @return ResponseInterface
     */
    public function deliverRequest(RequestInterface $request, array $options = [])
    {
        if (!self::$client) {
            self::$client = self::getClient();
        }

        return self::$client->send($request, $options);
    }

    /**
     * @return array
     */
    public function getConfigAsArray()
    {
        return [
            'httpHandler' => [$this, 'deliverRequest']
        ];
    }

    /**
     * @return ClientInterface
     */
    private static function getClient()
    {
        // configure and return guzzle client
        $stack = new HandlerStack();
        $stack->setHandler(\GuzzleHttp\choose_handler());
        $stack->push(new TraceMiddleware());

        return new Client(['handler' => $stack]);
    }
}
