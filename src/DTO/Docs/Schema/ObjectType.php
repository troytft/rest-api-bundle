<?php

namespace RestApiBundle\DTO\Docs\Schema;

use RestApiBundle;

class ObjectType implements RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface
{
    /**
     * @var string
     */
    private $class;

    /**
     * @var array<string, RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface>
     */
    private $properties;

    /**
     * @var bool
     */
    private $nullable;

    public function getClass(): string
    {
        return $this->class;
    }

    public function setClass(string $class)
    {
        $this->class = $class;

        return $this;
    }

    /**
     * @return array<string, RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    /**
     * @param array<string, RestApiBundle\DTO\Docs\Schema\SchemaTypeInterface> $properties
     *
     * @return $this
     */
    public function setProperties(array $properties)
    {
        $this->properties = $properties;

        return $this;
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
