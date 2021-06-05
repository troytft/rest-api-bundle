<?php

namespace RestApiBundle\Model\Mapper\Schema;

use function in_array;

final class Type
{
    private const TYPE_COLLECTION = 'collection';
    private const TYPE_OBJECT = 'object';
    private const TYPE_STRING = 'string';
    private const TYPE_INT = 'int';
    private const TYPE_BOOL = 'bool';
    private const TYPE_FLOAT = 'float';

    /** @var array<string, self> */
    private array $properties = [];
    private ?string $class;
    private bool $isNullable;
    private ?string $transformerClass;
    private array $transformerOptions = [];
    private ?string $setterName = null;
    private string $type;
    private ?self $collectionValuesType = null;

    private function __construct()
    {
    }

    public function isScalar(): bool
    {
        return in_array($this->type, [
            self::TYPE_STRING,
            self::TYPE_INT,
            self::TYPE_BOOL,
            self::TYPE_FLOAT,
        ], true);
    }

    public function isCollection(): bool
    {
        return $this->type === self::TYPE_COLLECTION;
    }

    public function isObject(): bool
    {
        return $this->type === self::TYPE_OBJECT;
    }

    public static function createScalar(
        string $type,
        bool $isNullable,
        ?string $transformerClass,
        array $transformerOptions = [],
        ?string $setterName = null
    ): self {
        $instance = new self();
        $instance->type = $type;
        $instance->isNullable = $isNullable;
        $instance->transformerClass = $transformerClass;
        $instance->transformerOptions = $transformerOptions;
        $instance->setterName = $setterName;

        return $instance;
    }

    /**
     * @param string $class
     * @param array<string, self> $properties
     * @param bool $isNullable
     * @param string|null $setterName
     *
     * @return self
     */
    public static function createObject(
        string $class,
        array $properties,
        bool $isNullable,
        ?string $setterName = null
    ): self {
        $instance = new self();
        $instance->class = $class;
        $instance->type = self::TYPE_OBJECT;
        $instance->isNullable = $isNullable;
        $instance->properties = $properties;
        $instance->setterName = $setterName;

        return $instance;
    }

    public static function createCollection(
        self $valuesType,
        bool $isNullable,
        ?string $setterName = null
    ): self {
        $instance = new self();
        $instance->collectionValuesType = $valuesType;
        $instance->type = self::TYPE_COLLECTION;
        $instance->isNullable = $isNullable;
        $instance->setterName = $setterName;

        return $instance;
    }

    /**
     * @return array<string, self>
     */
    public function getProperties(): array
    {
        return $this->properties;
    }

    public function getClass(): ?string
    {
        return $this->class;
    }

    public function getIsNullable(): bool
    {
        return $this->isNullable;
    }

    public function getTransformerClass(): ?string
    {
        return $this->transformerClass;
    }

    public function getTransformerOptions(): array
    {
        return $this->transformerOptions;
    }

    public function getSetterName(): ?string
    {
        return $this->setterName;
    }

    public function getCollectionValuesType(): ?self
    {
        return $this->collectionValuesType;
    }
}
