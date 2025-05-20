<?php
declare(strict_types=1);

namespace RestApiBundle\Model\Mapper;

final class Schema
{
    public const ARRAY_TYPE = 'array';
    public const MODEL_TYPE = 'model';
    public const TRANSFORMER_AWARE_TYPE = 'transformer-aware';
    public const UPLOADED_FILE_TYPE = 'uploaded-file';

    /** @var array<string, self> */
    public array $properties = [];
    public ?string $class = null;
    public bool $isNullable;
    public ?string $transformerClass = null;
    public array $transformerOptions = [];
    public string $type;
    public ?self $valuesType = null;
    public ?string $propertySetterName = null;
    public ?string $propertyGetterName = null;

    private function __construct()
    {
    }

    public static function createTransformerType(
        string $transformerClass,
        bool $isNullable,
        array $transformerOptions = [],
    ): self {
        $instance = new self();
        $instance->type = self::TRANSFORMER_AWARE_TYPE;
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

    public static function createUploadedFileType(bool $isNullable): self
    {
        $instance = new self();
        $instance->type = self::UPLOADED_FILE_TYPE;
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
            'propertyGetterName',
        ];
    }
}
