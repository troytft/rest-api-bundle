<?php

namespace RestApiBundle\DTO\Docs\Type;

use RestApiBundle;

class ArrayType implements RestApiBundle\DTO\Docs\Type\TypeInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Type\TypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(RestApiBundle\DTO\Docs\Type\TypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\Docs\Type\TypeInterface
    {
        return $this->innerType;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
