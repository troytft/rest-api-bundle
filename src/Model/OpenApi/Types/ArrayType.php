<?php

namespace RestApiBundle\Model\OpenApi\Types;

use RestApiBundle;

class ArrayType implements RestApiBundle\Model\OpenApi\Types\TypeInterface
{
    /**
     * @var RestApiBundle\Model\OpenApi\Types\TypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(RestApiBundle\Model\OpenApi\Types\TypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\Model\OpenApi\Types\TypeInterface
    {
        return $this->innerType;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
