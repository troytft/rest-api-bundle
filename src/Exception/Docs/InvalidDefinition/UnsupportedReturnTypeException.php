<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class UnsupportedReturnTypeException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('Unsupported return type.');
    }
}
