<?php

namespace RestApiBundle\Manager;

use Mapper;
use RestApiBundle\Exception\RequestModelMappingException;
use RestApiBundle\RequestModelInterface;
use function get_class;
use function sprintf;

class RequestModelManager
{
    /**
     * @var Mapper\Mapper
     */
    private $mapper;

    public function __construct()
    {
        $this->mapper = new Mapper\Mapper();
    }

    public function handleRequest(RequestModelInterface $requestModel, array $data): void
    {
        try {
            $this->mapper->map($requestModel, $data);
        } catch (Mapper\Exception\ExceptionInterface $exception) {
            if ($exception instanceof Mapper\Exception\MappingValidation\CollectionRequiredException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be collection.']]);
            } elseif ($exception instanceof Mapper\Exception\MappingValidation\ObjectRequiredException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be object.']]);
            } elseif ($exception instanceof Mapper\Exception\MappingValidation\ScalarRequiredException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be scalar.']]);
            } elseif ($exception instanceof Mapper\Exception\MappingValidation\UndefinedKeyException) {
                throw new RequestModelMappingException([$exception->getPathAsString() => ['Key is not defined in model.']]);
            } elseif ($exception instanceof Mapper\Exception\Transformer\WrappedTransformerException) {
                $previousException = $exception->getPrevious();

                if ($previousException instanceof Mapper\Exception\Transformer\BooleanRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be boolean.']]);
                } elseif ($previousException instanceof Mapper\Exception\Transformer\FloatRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be float.']]);
                } elseif ($exception instanceof Mapper\Exception\Transformer\IntegerRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be integer.']]);
                } elseif ($exception instanceof Mapper\Exception\Transformer\InvalidDateFormatException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [sprintf('This value should be valid date with format "%s".', $exception->getFormat())]]);
                } elseif ($exception instanceof Mapper\Exception\Transformer\InvalidDateTimeFormatException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => [sprintf('This value should be valid date time with format "%s".', $exception->getFormat())]]);
                } elseif ($exception instanceof Mapper\Exception\Transformer\IntegerRequiredException) {
                    throw new RequestModelMappingException([$exception->getPathAsString() => ['This value should be integer.']]);
                } else {
                    throw new \InvalidArgumentException();
                }
            } else {
                throw new \RuntimeException(sprintf('Unhandled exception %s %s', get_class($exception), $exception->getMessage()));
            }
        }
    }
}
