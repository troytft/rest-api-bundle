<?php

declare(strict_types=1);

namespace RestApiBundle\EventSubscriber;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\InputBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\KernelEvents;

use function json_decode;

class BodySubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::REQUEST => 'onEvent'
        ];
    }

    public function onEvent(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!$this->isSupportedRequest($request)) {
            return;
        }

        $decodedContent = json_decode($request->getContent(), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException();
        }

        $request->request = new InputBag((array) $decodedContent);
    }

    private function isSupportedRequest(Request $request): bool
    {
        if (count($request->request->all()) !== 0) {
            return false;
        }

        if (!in_array($request->getMethod(), ['POST', 'PUT', 'PATCH', 'DELETE'], true)) {
            return false;
        }

        if ($request->getContent() === '') {
            return false;
        }

        return true;
    }
}
