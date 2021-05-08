<?php

namespace RestApiBundle\Exception\OpenApi\InvalidDefinition;

use RestApiBundle;

class UnsupportedReturnTypeException extends RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('Unsupported return type.');
    }
}
