<?php

namespace RestApiBundle\EventSubscriber;

use RestApiBundle;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    /**
     * @var RestApiBundle\Manager\RequestModelManager
     */
    private $requestModelManager;

    public function __construct(RestApiBundle\Manager\RequestModelManager $requestModelManager)
    {
        $this->requestModelManager = $requestModelManager;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onEvent'
        ];
    }

    public function onEvent(GetResponseForExceptionEvent $event)
    {
        if (!$this->requestModelManager->getIsHandleRequestModelMappingException()) {
            return;
        }

        $exception = $event->getException();

        if ($exception instanceof RestApiBundle\Exception\RequestModelMappingException) {
            $event
                ->setResponse(new JsonResponse(['properties' => $exception->getProperties()], 400));

            return;
        }
    }
}
