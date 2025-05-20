<?php
declare(strict_types=1);

namespace RestApiBundle\EventSubscriber;

use RestApiBundle;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ViewEvent;

class ResponseSubscriber implements EventSubscriberInterface
{
    private RestApiBundle\Services\ResponseModel\ResponseHandler $responseHandler;

    public function __construct(RestApiBundle\Services\ResponseModel\ResponseHandler $responseHandler)
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
