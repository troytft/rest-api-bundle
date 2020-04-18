<?php

namespace RestApiBundle\Exception\Docs\InvalidDefinition;

use RestApiBundle;
use function sprintf;

class NotMatchedRoutePlaceholderParameterException extends RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException
{
    public function __construct(string $placeholder)
    {
        parent::__construct(sprintf('Associated parameter for placeholder %s not matched.', $placeholder));
    }
}
