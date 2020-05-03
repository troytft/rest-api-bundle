<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;
use function sprintf;

class EmptyRoutePathException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct()
    {
        parent::__construct('Route has empty path.');
    }
}
