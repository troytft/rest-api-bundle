<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class IntegerType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public ?bool $nullable;

    public function __construct(array $options = [], ?bool $nullable = null)
    {
        $this->nullable = $options['nullable'] ?? $nullable;
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\IntegerTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }

    public function getIsNullable(): ?bool
    {
        return $this->nullable;
    }
}
