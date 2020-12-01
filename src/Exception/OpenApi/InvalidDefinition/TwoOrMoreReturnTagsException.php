<?php

namespace RestApiBundle\Exception\OpenApi\InvalidDefinition;

use RestApiBundle;

class TwoOrMoreReturnTagsException extends RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('DocBlock contains two or more return tags.');
    }
}
