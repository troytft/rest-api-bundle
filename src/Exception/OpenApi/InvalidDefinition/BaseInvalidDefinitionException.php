<?php

namespace RestApiBundle\Exception\OpenApi\InvalidDefinition;

abstract class BaseInvalidDefinitionException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
