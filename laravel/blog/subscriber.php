<?php

require './vendor/autoload.php';

use Google\Cloud\PubSub\PubSubClient;

$pubsub = new PubSubClient();
$topic = $pubsub->topic('test-topic');

var_dump($topic->info());

$subscription = $topic->subscription('cli');
if (!$subscription->exists()) {
    $subscription->create();
}

while (true) {
    $messages = $subscription->pull();

    var_dump($messages);


    if (count($messages) > 0) {
        foreach ($messages as $message) {
            var_dump($message->data());
            var_dump($message->attributes());
        }
        $subscription->acknowledgeBatch($messages);
    }
}
