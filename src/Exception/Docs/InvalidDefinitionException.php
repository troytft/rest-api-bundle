<?php

namespace RestApiBundle\Exception\Docs;

use RestApiBundle;
use function sprintf;

class InvalidDefinitionException extends \Exception
{
    /**
     * @var string
     */
    private $context;

    public function __construct(RestApiBundle\Exception\Docs\InvalidDefinition\BaseInvalidDefinitionException $previous, string $context)
    {
        $this->previous = $previous;
        $this->context = $context;

        parent::__construct(sprintf('Error: %s, Context: %s', $previous->getMessage(), $context), 0, $previous);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
