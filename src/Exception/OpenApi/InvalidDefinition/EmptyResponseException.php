<?php

namespace RestApiBundle\Exception\OpenApi\InvalidDefinition;

use RestApiBundle;

class EmptyResponseException extends RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('Return type not found in docBlock and type-hint.');
    }
}
