<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class StringType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public bool $nullable = false;
    public bool $trim = false;

    public function __construct(array $options = [], bool $trim = false, bool $nullable = false)
    {
        $this->trim = $options['trim'] ?? $trim;
        $this->nullable = $options['nullable'] ?? $nullable;
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\StringTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\StringTransformer::TRIM_OPTION => $this->trim,
        ];
    }

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
