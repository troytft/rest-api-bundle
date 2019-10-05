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
                throw new RequestModelMappingException([$exception->getPathAsString() => ['Value should be a collection']]);
            } else {
                throw new \RuntimeException(sprintf('Unhandled exception %s %s', get_class($exception), $exception->getMessage()));
            }
        }
    }
}
