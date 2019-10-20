<?php

namespace RestApiBundle\EventSubscriber;

use RestApiBundle;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onEvent'
        ];
    }

    public function onEvent(GetResponseForExceptionEvent $event)
    {
        $exception = $event->getException();

        if ($exception instanceof RestApiBundle\Exception\RequestModelMappingException) {
            $event
                ->setResponse(new JsonResponse(['properties' => $exception->getProperties()], 400));

            return;
        }
    }
}
