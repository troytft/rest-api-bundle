<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class ArrayType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
{
    /**
     * @var RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
     */
    private $innerType;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface $innerType, bool $nullable)
    {
        $this->innerType = $innerType;
        $this->nullable = $nullable;
    }

    public function getInnerType(): RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
    {
        return $this->innerType;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }
}
