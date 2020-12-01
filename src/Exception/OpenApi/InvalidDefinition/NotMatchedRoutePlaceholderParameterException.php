<?php

namespace RestApiBundle\Exception\OpenApi\InvalidDefinition;

use RestApiBundle;
use function sprintf;

class NotMatchedRoutePlaceholderParameterException extends RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct(string $placeholder)
    {
        parent::__construct(sprintf('Associated parameter for placeholder %s not matched.', $placeholder));
    }
}
