<?php

namespace RestApiBundle\Model\Mapper;

final class Schema
{
    private const ARRAY_TYPE = 'array';
    private const MODEL_TYPE = 'model';
    private const TRANSFORMER_AWARE_TYPE = 'transformer-aware';

    /** @var array<string, self> */
    private array $properties = [];
    private ?string $class;
    private bool $isNullable;
    private ?string $transformerClass;
    private array $transformerOptions = [];
    private string $type;
    private ?self $valuesType = null;
    private ?string $propertySetterName = null;

    private function __construct()
    {
    }

    public function isTransformerAwareType(): bool
    {
        return $this->type === self::TRANSFORMER_AWARE_TYPE;
    }

    public static function createTransformerAwareType(
        string $transformerClass,
        array $transformerOptions,
        bool $isNullable
    ): self {
        $instance = new self();
        $instance->type = self::TRANSFORMER_AWARE_TYPE;
        $instance->isNullable = $isNullable;
        $instance->transformerClass = $transformerClass;
        $instance->transformerOptions = $transformerOptions;

        return $instance;
    }

    public function isModelType(): bool
    {
        return $this->type === self::MODEL_TYPE;
    }

    /**
     * @param string $class
     * @param array<string, self> $properties
     * @param bool $isNullable
     *
     * @return self
     */
    public static function createModelType(
        string $class,
        array $properties,
        bool $isNullable
    ): self {
        $instance = new self();
        $instance->type = self::MODEL_TYPE;
        $instance->class = $class;
        $instance->isNullable = $isNullable;
        $instance->properties = $properties;

        return $instance;
    }

    public function isArrayType(): bool
    {
        return $this->type === self::ARRAY_TYPE;
    }

    public static function createArrayType(
        self $valuesType,
        bool $isNullable
    ): self {
        $instance = new self();
        $instance->valuesType = $valuesType;
        $instance->type = self::ARRAY_TYPE;
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

    public function getValuesType(): ?self
    {
        return $this->valuesType;
    }

    public function getPropertySetterName(): ?string
    {
        return $this->propertySetterName;
    }

    public function setPropertySetterName(?string $propertySetterName)
    {
        $this->propertySetterName = $propertySetterName;

        return $this;
    }
}
