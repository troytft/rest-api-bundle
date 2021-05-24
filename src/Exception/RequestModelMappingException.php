<?php

namespace RestApiBundle\Exception;

class RequestModelMappingException extends \RuntimeException implements ExceptionInterface
{
    private array $properties;

    public function __construct(array $properties)
    {
        $this->properties = $properties;

        parent::__construct();
    }

    public function getProperties(): array
    {
        return $this->properties;
    }
}
