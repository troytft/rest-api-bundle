<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;

class ArrayType implements RestApiBundle\DTO\OpenApi\Schema\TypeInterface
{
    /**
     * @var RestApiBundle\DTO\OpenApi\Schema\TypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(RestApiBundle\DTO\OpenApi\Schema\TypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\OpenApi\Schema\TypeInterface
    {
        return $this->innerType;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
