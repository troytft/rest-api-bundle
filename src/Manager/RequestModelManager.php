<?php

namespace RestApiBundle\Manager;

use Mapper;
use RestApiBundle\Exception\RequestModelMappingException;
use RestApiBundle\RequestModelInterface;
use Symfony\Component\Translation\TranslatorInterface;
use function get_class;
use function sprintf;

class RequestModelManager
{
    /**
     * @var Mapper\Mapper
     */
    private $mapper;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(TranslatorInterface $translator)
    {
        $this->mapper = new Mapper\Mapper();
        $this->translator = $translator;
    }

    public function handleRequest(RequestModelInterface $requestModel, array $data): void
    {
        try {
            $this->mapper->map($requestModel, $data);
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
                } elseif ($previousException instanceof Mapper\Exception\Transformer\InvalidDateTimeFormatException) {
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

            throw new RequestModelMappingException([$path => [$message]]);
        }
    }
}
