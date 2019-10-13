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
            if ($exception instanceof Mapper\Exception\MappingValidation\CollectionRequiredException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.collection_required')]]);
            } elseif ($exception instanceof Mapper\Exception\MappingValidation\ObjectRequiredException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.object_required')]]);
            } elseif ($exception instanceof Mapper\Exception\MappingValidation\ScalarRequiredException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.scalar_required')]]);
            } elseif ($exception instanceof Mapper\Exception\MappingValidation\UndefinedKeyException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.undefined_key')]]);
            } elseif ($exception instanceof Mapper\Exception\Transformer\WrappedTransformerException) {
                $previousException = $exception->getPrevious();
                if ($previousException instanceof Mapper\Exception\Transformer\BooleanRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.boolean_required')]]);
                } elseif ($previousException instanceof Mapper\Exception\Transformer\FloatRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.float_required')]]);
                } elseif ($previousException instanceof Mapper\Exception\Transformer\StringRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.string_required')]]);
                } elseif ($previousException instanceof Mapper\Exception\Transformer\IntegerRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.integer_required')]]);
                } elseif ($previousException instanceof Mapper\Exception\Transformer\InvalidDateFormatException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.invalid_date_format', ['{format}' => $previousException->getFormat()])]]);
                } elseif ($previousException instanceof Mapper\Exception\Transformer\InvalidDateTimeFormatException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [$this->translator->trans('mapper_exception.invalid_date_time_format', ['{format}' => $previousException->getFormat()])]]);
                } else {
                    throw new \RuntimeException(sprintf('Unhandled exception %s %s', get_class($previousException), $previousException->getMessage()));
                }
            } else {
                throw new \RuntimeException(sprintf('Unhandled exception %s %s', get_class($exception), $exception->getMessage()));
            }
        }
    }
}
