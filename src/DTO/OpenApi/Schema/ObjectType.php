<?php

namespace RestApiBundle\DTO\OpenApi\Schema;

use RestApiBundle;

class ObjectType implements RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface
{
    /**
     * @var array<string, RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface>
     */
    private $properties;

    /**
     * @var bool
     */
    private $nullable;

    public function __construct(array $properties, bool $nullable)
    {
        $this->properties = $properties;
        $this->nullable = $nullable;
    }

    /**
     * @return array<string, RestApiBundle\DTO\OpenApi\Schema\SchemaTypeInterface>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getNullable(): bool
    {
        return $this->nullable;
    }

    public function setNullable(bool $nullable)
    {
        $this->nullable = $nullable;

        return $this;
    }
}
