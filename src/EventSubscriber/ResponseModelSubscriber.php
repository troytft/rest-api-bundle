<?php

namespace Common\EventSubscriber;

use Common;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;

class ResponseSerializationSubscriber implements EventSubscriberInterface
{
    /**
     * @var Common\HelperService\ResponseModelSerializer
     */
    private $responseModelSerializer;

    /**
     * @param Common\HelperService\ResponseModelSerializer $responseModelSerializer
     */
    public function __construct(Common\HelperService\ResponseModelSerializer $responseModelSerializer)
    {
        $this->responseModelSerializer = $responseModelSerializer;
    }

    /**
     * {@inheritdoc}
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'handle'
        ];
    }

    /**
     * @param GetResponseForControllerResultEvent $event
     */
    public function handle(GetResponseForControllerResultEvent $event)
    {
        if (!($result = $event->getControllerResult()) instanceof Response) {
            $requestAttributes = $event->getRequest()->attributes;

            if (!$httpStatus = $requestAttributes->get('_annotation_http_status')) {
                $httpStatus = $result !== null ? 200 : 204;
            }

            $defaultHeaders = ['Content-Type' => 'application/json'];
            $headers = array_merge($defaultHeaders, (array) $requestAttributes->get('_response_headers'));

            $event->setResponse(new Response($this->serializeResponse($result), $httpStatus, $headers));

            return;
        }
    }

    /**
     * @param mixed $value
     *
     * @return null|string
     */
    private function serializeResponse($value): ?string
    {
        if ($value === null) {
            return null;
        }

        if (!is_array($value)) {
            return $this->responseModelSerializer->toJson($value);
        }

        if (empty($value)) {
            return '[]';
        }

        if (array_keys($value) !== range(0, count($value) - 1)) {
            return $this->responseModelSerializer->toJson($value);
        }

        $chunks = [];
        foreach ($value as $key => $item) {
            $chunks[] = $this->responseModelSerializer->toJson($item);
        }

        return '[' . join(',', $chunks) . ']';
    }
}
