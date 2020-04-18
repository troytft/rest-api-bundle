<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;

class EmptyReturnTypeException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('Return type not found in docBlock and type-hint.');
    }
}
