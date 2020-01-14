<?php

namespace RestApiBundle\EventSubscriber;

use RestApiBundle;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use function array_keys;
use function count;
use function join;
use function range;

class ResponseModelSubscriber implements EventSubscriberInterface
{
    /**
     * @var RestApiBundle\Manager\ResponseModel\ResponseModelSerializer
     */
    private $responseModelSerializer;

    public function __construct(RestApiBundle\Manager\ResponseModel\ResponseModelSerializer $responseModelSerializer)
    {
        $this->responseModelSerializer = $responseModelSerializer;
    }

    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => 'handle'
        ];
    }

    public function handle(GetResponseForControllerResultEvent $event)
    {
        $result = $event->getControllerResult();

        if (!$result instanceof Response) {
            $defaultHeaders = [
                'Content-Type' => 'application/json',
            ];
            $headers = array_merge($defaultHeaders, $event->getRequest()->attributes->get('_response_headers', []));
            $httpStatus = $result !== null ? 200 : 204;

            $event->setResponse(new Response($this->serializeResponse($result), $httpStatus, $headers));

            return;
        }
    }

    private function serializeResponse($value): ?string
    {
        if ($value === null) {
            $result = null;
        } elseif ($value instanceof RestApiBundle\ResponseModelInterface) {
            $result = $this->responseModelSerializer->toJson($value);
        } elseif (is_array($value)) {
            if (!$this->isPlainArray($value)) {
                throw new \InvalidArgumentException('Associative arrays are not allowed.');
            }

            $chunks = [];

            foreach ($value as $item) {
                if (!$item instanceof RestApiBundle\ResponseModelInterface) {
                    throw new \InvalidArgumentException('The collection should consist of response models.');
                }

                $chunks[] = $this->responseModelSerializer->toJson($item);
            }

            $result = '[' . join(',', $chunks) . ']';
        } else {
            throw new \InvalidArgumentException();
        }

        return $result;
    }

    private function isPlainArray(array $items): bool
    {
        return array_keys($items) === range(0, count($items) - 1);
    }
}
