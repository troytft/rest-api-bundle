<?php

namespace RestApiBundle\EventSubscriber;

use RestApiBundle;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    /**
     * @var RestApiBundle\Services\Response\ResponseHandler
     */
    private $responseHandler;

    public function __construct(RestApiBundle\Services\Response\ResponseHandler $responseHandler)
    {
        $this->responseHandler = $responseHandler;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'handle'
        ];
    }

    public function handle(ViewEvent $event)
    {
        $this->responseHandler->handleControllerResultEvent($event);
    }
}
