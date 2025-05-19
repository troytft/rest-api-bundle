<?php declare(strict_types=1);

namespace RestApiBundle\Services\ResponseModel;

use RestApiBundle;
use Symfony\Component\HttpFoundation;
use Symfony\Component\HttpKernel;

use function array_merge;

class ResponseHandler
{
    public function __construct(
        private RestApiBundle\Services\SettingsProvider $settingsProvider,
        private RestApiBundle\Services\ResponseModel\Serializer $serializer
    ) {
    }

    public function handleControllerResultEvent(HttpKernel\Event\ViewEvent $event): void
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
                $this->serializer->serialize($result),
                $result === null ? 204 : 200,
                array_merge($defaultHeaders, $event->getRequest()->attributes->get('_response_headers', [])),
            ));
        }
    }
}
