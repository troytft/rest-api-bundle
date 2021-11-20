<?php

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ViewEvent;

use function array_keys;
use function array_merge;
use function is_array;
use function join;
use function range;

class ResponseHandler
{
    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private RestApiBundle\Services\ResponseModel\Serializer $serializer
    ) {
    }

    public function handleControllerResultEvent(ViewEvent $event)
    {
        if (!$this->settingsProvider->isResponseHandlerEnabled()) {
            return;
        }

        $result = $event->getControllerResult();
        if (!$result instanceof Response) {
            $defaultHeaders = [
                'Content-Type' => 'application/json',
            ];
            $headers = array_merge($defaultHeaders, $event->getRequest()->attributes->get('_response_headers', []));
            $httpStatus = $result !== null ? 200 : 204;

            $event->setResponse(new Response($this->serializeResponse($result), $httpStatus, $headers));
        }
    }

    private function serializeResponse($value): ?string
    {
        if ($value === null) {
            $result = null;
        } elseif ($value instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface) {
            $result = $this->serializer->toJson($value);
        } elseif (is_array($value)) {
            if (!$this->isPlainArray($value)) {
                throw new \InvalidArgumentException('Associative arrays are not allowed.');
            }

            $chunks = [];

            foreach ($value as $item) {
                if (!$item instanceof RestApiBundle\Mapping\ResponseModel\ResponseModelInterface) {
                    throw new \InvalidArgumentException('The collection should consist of response models.');
                }

                $chunks[] = $this->serializer->toJson($item);
            }

            $result = '[' . join(',', $chunks) . ']';
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function isPlainArray(array $array): bool
    {
        return empty($array) || array_keys($array) === range(0, count($array) - 1);
    }
}
