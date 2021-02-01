<?php

namespace RestApiBundle\DTO\Docs\Types;

use RestApiBundle;

class ArrayType implements RestApiBundle\DTO\Docs\Types\TypeInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Types\TypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(RestApiBundle\DTO\Docs\Types\TypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\Docs\Types\TypeInterface
    {
        return $this->innerType;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
