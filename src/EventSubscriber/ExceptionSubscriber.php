<?php declare(strict_types=1);

namespace RestApiBundle\EventSubscriber;

use RestApiBundle;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;

class ExceptionSubscriber implements EventSubscriberInterface
{
    public function __construct(private RestApiBundle\Services\SettingsProvider $settingsProvider)
    {
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::EXCEPTION => 'onEvent'
        ];
    }

    public function onEvent(ExceptionEvent $event)
    {
        if (!$this->settingsProvider->isRequestValidationExceptionHandlerEnabled()) {
            return;
        }

        $exception = $event->getThrowable();
        if (!$exception instanceof RestApiBundle\Exception\Mapper\MappingException) {
            return;
        }

        $event->setResponse(new JsonResponse(['properties' => $exception->getProperties()], 400));
    }
}
