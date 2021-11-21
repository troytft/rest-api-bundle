<?php

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;

use function array_merge;
use function is_array;
use function join;

class ResponseHandler
{
    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private RestApiBundle\Services\ResponseModel\Serializer $serializer
    ) {
    }

    public function handleControllerResultEvent(HttpKernel\Event\ViewEvent $event)
    {
        if (!$this->settingsProvider->isResponseHandlerEnabled()) {
            return;
        }

        $result = $event->getControllerResult();
        if (!$result instanceof HttpFoundation\Response) {
            $defaultHeaders = [
                'Content-Type' => 'application/json',
            ];

            $event->setResponse(new HttpFoundation\Response(
                $this->serializeResponse($result),
                $result === null ? 204 : 200,
                array_merge($defaultHeaders, $event->getRequest()->attributes->get('_response_headers', [])),
            ));
        }
    }

    private function serializeResponse($value): ?string
    {
        if ($value === null) {
            $result = null;
        } elseif ($value instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface) {
            $result = $this->serializer->toJson($value);
        } elseif (is_array($value)) {
            if (!array_is_list($value)) {
                throw new \InvalidArgumentException('Associative arrays are not allowed');
            }

            $chunks = [];

            foreach ($value as $item) {
                if (!$item instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface) {
                    throw new \InvalidArgumentException('The collection should consist of response models');
                }

                $chunks[] = $this->serializer->toJson($item);
            }

            $result = '[' . join(',', $chunks) . ']';
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }
}
