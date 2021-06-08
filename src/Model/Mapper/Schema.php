<?php

namespace RestApiBundle\Model\Mapper;

final class Schema
{
    public const ARRAY_TYPE = 'array';
    public const MODEL_TYPE = 'model';
    public const TRANSFORMER_AWARE_TYPE = 'transformer-aware';

    /** @var array<string, self> */
    public array $properties = [];
    public ?string $class = null;
    public bool $isNullable;
    public ?string $transformerClass = null;
    public array $transformerOptions = [];
    public string $type;
    public ?self $valuesType = null;
    public ?string $propertySetterName = null;

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
     * @return string[]
     */
    public function __sleep()
    {
        return [
            'type',
            'properties',
            'class',
            'isNullable',
            'transformerClass',
            'transformerOptions',
            'valuesType',
            'propertySetterName',
        ];
    }
}
