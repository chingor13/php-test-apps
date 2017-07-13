<?php

namespace Google\Symfony;

use Google\Cloud\Trace\RequestTracer;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\FilterControllerEvent;
use Symfony\Component\HttpKernel\Event\FilterControllerArgumentsEvent;
use Symfony\Component\HttpKernel\Event\FinishRequestEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class EventSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::CONTROLLER_ARGUMENTS => 'handleArguments'
            // KernelEvents::REQUEST => 'processRequest',
            // KernelEvents::CONTROLLER => 'processController',
            // KernelEvents::VIEW => 'processView',
            // KernelEvents::FINISH_REQUEST => 'processFinish'
        ];
    }

    public function handleArguments(FilterControllerArgumentsEvent $event)
    {
        $controller = $event->getController();
        if (is_array($controller)) {
            RequestTracer::instance()->tracer()->addRootLabel('controller', get_class($controller[0]));
            RequestTracer::instance()->tracer()->addRootLabel('action', $controller[1]);
        } else {
            RequestTracer::instance()->tracer()->addRootLabel('controller', '(closure)');
        }
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
        RequestTracer::endSpan();
        RequestTracer::startSpan(['name' => 'view']);
    }

    public function processFinish(FinishRequestEvent $event)
    {
        // should complete the controller or view span
        RequestTracer::endSpan();

        // should complete the request span
        RequestTracer::endSpan();
    }

}
