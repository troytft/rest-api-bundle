<?php

namespace RestApiBundle\Services\RequestModel;

use RestApiBundle;
use Symfony\Contracts\Translation\TranslatorInterface;

use function get_class;
use function sprintf;

class RequestHandler
{
    private TranslatorInterface $translator;
    private RestApiBundle\Services\Mapper\Mapper $mapper;
    private RestApiBundle\Services\RequestModel\RequestModelValidator $requestModelValidator;

    public function __construct(
        TranslatorInterface $translator,
        RestApiBundle\Services\Mapper\Mapper $mapper,
        RestApiBundle\Services\RequestModel\RequestModelValidator $requestModelValidator
    ) {
        $this->translator = $translator;
        $this->mapper = $mapper;
        $this->requestModelValidator = $requestModelValidator;
    }

    /**
     * @throws RestApiBundle\Exception\RequestModelMappingException
     */
    public function handle(RestApiBundle\Mapping\RequestModel\RequestModelInterface $requestModel, array $data, ?RestApiBundle\Model\Mapper\Context $context = null): void
    {
        try {
            $this->mapper->map($requestModel, $data, $context);
        } catch (RestApiBundle\Exception\Mapper\StackedMappingException $exception) {
            throw $this->convertStackedMappingException($exception);
        }

        $validationErrors = $this->requestModelValidator->validate($requestModel);
        if ($validationErrors) {
            throw new RestApiBundle\Exception\RequestModelMappingException($validationErrors);
        }
    }

    private function convertStackedMappingException(RestApiBundle\Exception\Mapper\StackedMappingException $exception): RestApiBundle\Exception\RequestModelMappingException
    {
        $result = [];

        foreach ($exception->getExceptions() as $stackableException) {
            $translationParameters = [];

            if ($stackableException instanceof RestApiBundle\Exception\Mapper\Transformer\WrappedTransformerException) {
                $path = $stackableException->getPathAsString();
                $previousException = $stackableException->getPrevious();
                $translationId = get_class($previousException);

                if ($previousException instanceof RestApiBundle\Exception\Mapper\Transformer\InvalidDateFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }

                if ($previousException instanceof RestApiBundle\Exception\Mapper\Transformer\InvalidDateTimeFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }
            } else {
                $path = $stackableException->getPathAsString();
                $translationId = get_class($stackableException);
            }

            $message = $this->translator->trans($translationId, $translationParameters, 'exceptions');
            if ($message === $translationId) {
                throw new \InvalidArgumentException(sprintf('Can\'t find translation with key "%s"', $translationId));
            }

            $result[$path] = [$message];
        }

        return new RestApiBundle\Exception\RequestModelMappingException($result);
    }
}
