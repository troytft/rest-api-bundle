<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class ArrayType implements RestApiBundle\DTO\Docs\Schema\TypeInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Schema\TypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(RestApiBundle\DTO\Docs\Schema\TypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\Docs\Schema\TypeInterface
    {
        return $this->innerType;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
