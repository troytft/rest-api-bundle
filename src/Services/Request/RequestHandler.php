<?php

namespace RestApiBundle\Services\Request;

use Mapper;
use RestApiBundle;
use Symfony\Component\Translation\TranslatorInterface;
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
        } catch (Mapper\Exception\ExceptionInterface $exception) {
            $translationParameters = [];

            if ($exception instanceof Mapper\Exception\MappingValidation\MappingValidationExceptionInterface || $exception instanceof Mapper\Exception\MappingValidation\UndefinedKeyException) {
                $path = $exception->getPathAsString();
                $translationId = get_class($exception);
            } elseif ($exception instanceof Mapper\Exception\Transformer\WrappedTransformerException) {
                $path = $exception->getPathAsString();
                $previousException = $exception->getPrevious();
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
                throw $exception;
            }

            $message = $this->translator->trans($translationId, $translationParameters, 'exceptions');

            if ($message === $translationId) {
                throw new \InvalidArgumentException(sprintf('Can\'t find translation with key "%s"', $translationId));
            }

            throw new RestApiBundle\Exception\RequestModelMappingException([$path => [$message]]);
        }
    }
}
