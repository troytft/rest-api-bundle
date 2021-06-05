<?php

namespace RestApiBundle\Model\Mapper;

final class Schema
{
    private const TYPE_COLLECTION = 'collection';
    private const TYPE_OBJECT = 'object';
    private const TYPE_SCALAR = 'scalar';

    /** @var array<string, self> */
    private array $properties = [];
    private ?string $class;
    private bool $isNullable;
    private ?string $transformerClass;
    private array $transformerOptions = [];
    private string $type;
    private ?self $collectionValuesType = null;

    private function __construct()
    {
    }

    public function isScalar(): bool
    {
        return $this->type === self::TYPE_SCALAR;
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
        string $transformerClass,
        array $transformerOptions,
        bool $isNullable
    ): self {
        $instance = new self();
        $instance->type = self::TYPE_SCALAR;
        $instance->isNullable = $isNullable;
        $instance->transformerClass = $transformerClass;
        $instance->transformerOptions = $transformerOptions;

        return $instance;
    }

    /**
     * @param string $class
     * @param array<string, self> $properties
     * @param bool $isNullable
     *
     * @return self
     */
    public static function createObject(
        string $class,
        array $properties,
        bool $isNullable
    ): self {
        $instance = new self();
        $instance->type = self::TYPE_OBJECT;
        $instance->class = $class;
        $instance->isNullable = $isNullable;
        $instance->properties = $properties;

        return $instance;
    }

    public static function createCollection(
        self $valuesType,
        bool $isNullable
    ): self {
        $instance = new self();
        $instance->collectionValuesType = $valuesType;
        $instance->type = self::TYPE_COLLECTION;
        $instance->isNullable = $isNullable;

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

    public function getCollectionValuesType(): ?self
    {
        return $this->collectionValuesType;
    }
}
