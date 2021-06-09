<?php

namespace RestApiBundle\Exception\OpenApi\InvalidDefinition;

use RestApiBundle;

class EmptyRoutePathException extends RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('Route has empty path.');
    }
}
