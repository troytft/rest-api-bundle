<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class DateTimeType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public bool $nullable = false;

    public ?string $format = null;

    public function __construct(array $options = [], ?string $format = null, bool $nullable = false)
    {
        $this->format = $options['format'] ?? $format;
        $this->nullable = $options['nullable'] ?? $nullable;
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION => $this->format,
        ];
    }

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
