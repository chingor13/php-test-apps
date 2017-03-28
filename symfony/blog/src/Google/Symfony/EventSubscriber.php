<?php

namespace Google\Symfony;

use Google\Cloud\Trace\RequestTracer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'processRequest',
            KernelEvents::CONTROLLER => 'processController',
            KernelEvents::VIEW => 'processView',
            KernelEvents::FINISH_REQUEST => 'processFinish'
        ];
    }

    public function processRequest(GetResponseEvent $event)
    {
        RequestTracer::startSpan(['name' => 'request']);
    }

    public function processController(FilterControllerEvent $event)
    {
        RequestTracer::startSpan(['name' => 'controller']);
    }

    public function processView(GetResponseForControllerResultEvent $event)
    {
        RequestTracer::finishSpan();
        RequestTracer::startSpan(['name' => 'view']);
    }

    public function processFinish(FinishRequestEvent $event)
    {
        // should complete the controller or view span
        RequestTracer::finishSpan();

        // should complete the request span
        RequestTracer::finishSpan();
    }

}
