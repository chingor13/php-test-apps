<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Google\Cloud\PubSub\PubSubClient;

class MessagesController extends Controller
{
    public function index(PubSubClient $pubsub)
    {
        $topic = $pubsub->topic('test-topic');
        $subscription = $topic->subscription('web');
        if (!$subscription->exists()) {
            $subscription->create();
        }

        $messages = $subscription->pull([
            'returnImmediately' => true
        ]);
        if (count($messages) > 0) {
            $subscription->acknowledgeBatch($messages);
        }
        return response()->json(array_map(function ($message) {
            return $message->attributes() + [
                'data' => $message->data()
            ];
        }, $messages));
    }

    public function send(PubSubClient $pubsub)
    {
        $topic = $pubsub->topic('test-topic');
        $publisher = $topic->batchPublisher();
        $publisher->publish([
            'data' => 'Some message',
            'attributes' => [
                'id' => '1',
                'userName' => 'John',
                'location' => 'Detroit'
            ]
        ]);
        return 'OK';
    }
}
