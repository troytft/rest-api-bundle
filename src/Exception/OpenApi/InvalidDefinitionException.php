<?php

namespace RestApiBundle\Exception\OpenApi;

use RestApiBundle;
use function sprintf;

class InvalidDefinitionException extends \Exception
{
    /**
     * @var string
     */
    private $context;

    public function __construct(RestApiBundle\Exception\OpenApi\InvalidDefinition\BaseInvalidDefinitionException $previous, string $context)
    {
        $this->context = $context;
        $message = sprintf('Error: %s, Context: %s', $previous->getMessage(), $context);

        parent::__construct($message, 0, $previous);
    }

    public function getContext(): string
    {
        return $this->context;
    }
}
