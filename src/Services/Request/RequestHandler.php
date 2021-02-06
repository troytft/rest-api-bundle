<?php

namespace RestApiBundle\Services\Request;

use Mapper;
use RestApiBundle;
use Symfony\Contracts\Translation\TranslatorInterface;

use function get_class;
use function sprintf;

class RequestHandler
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var RestApiBundle\Services\Request\MapperInitiator
     */
    private $mapperInitiator;

    /**
     * @var RestApiBundle\Services\SettingsProvider
     */
    private $settingsProvider;

    /**
     * @var RestApiBundle\Services\Request\RequestModelValidator
     */
    private $requestModelValidator;

    public function __construct(
        TranslatorInterface $translator,
        RestApiBundle\Services\Request\MapperInitiator $mapperInitiator,
        RestApiBundle\Services\SettingsProvider $settingsProvider,
        RestApiBundle\Services\Request\RequestModelValidator $requestModelValidator
    ) {
        $this->translator = $translator;
        $this->mapperInitiator = $mapperInitiator;
        $this->settingsProvider = $settingsProvider;
        $this->requestModelValidator = $requestModelValidator;
    }

    /**
     * @throws RestApiBundle\Exception\RequestModelMappingException
     */
    public function handle(RestApiBundle\RequestModelInterface $requestModel, array $data): void
    {
        $this->map($requestModel, $data);

        $validationErrors = $this->requestModelValidator->validate($requestModel);
        if ($validationErrors) {
            throw new RestApiBundle\Exception\RequestModelMappingException($validationErrors);
        }
    }

    /**
     * @throws RestApiBundle\Exception\RequestModelMappingException
     */
    private function map(RestApiBundle\RequestModelInterface $requestModel, array $data): void
    {
        try {
            $this->mapperInitiator->getMapper()->map($requestModel, $data);
        } catch (Mapper\Exception\StackedMappingException $exception) {
            throw $this->convertStackedMappingException($exception);
        }
    }

    private function convertStackedMappingException(Mapper\Exception\StackedMappingException $exception): RestApiBundle\Exception\RequestModelMappingException
    {
        $result = [];

        foreach ($exception->getExceptions() as $stackableException) {
            $translationParameters = [];

            if ($stackableException instanceof Mapper\Exception\Transformer\WrappedTransformerException) {
                $path = $stackableException->getPathAsString();
                $previousException = $stackableException->getPrevious();
                $translationId = get_class($previousException);

                if ($previousException instanceof Mapper\Exception\Transformer\InvalidDateFormatException) {
                    $translationParameters = [
                        '{format}' => $previousException->getFormat(),
                    ];
                }

                if ($previousException instanceof Mapper\Exception\Transformer\InvalidDateTimeFormatException) {
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
