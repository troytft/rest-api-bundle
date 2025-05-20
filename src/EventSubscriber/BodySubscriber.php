<?php

declare(strict_types=1);

namespace RestApiBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class BodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onEvent',
        ];
    }

    public function onEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isSupportedRequest($request)) {
            return;
        }

        $decodedContent = \json_decode($request->getContent(), true);
        if (JSON_ERROR_NONE !== json_last_error()) {
            throw new \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException();
        }

        $request->request = new InputBag((array) $decodedContent);
    }

    private function isSupportedRequest(Request $request): bool
    {
        if (0 !== count($request->request->all())) {
            return false;
        }

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if ('' === $request->getContent()) {
            return false;
        }

        return true;
    }
}
