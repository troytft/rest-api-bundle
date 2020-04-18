<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

abstract class BaseInvalidDefinitionException extends \Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
