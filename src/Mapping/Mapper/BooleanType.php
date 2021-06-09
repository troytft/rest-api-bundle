<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY | \Attribute::IS_REPEATABLE)]
class BooleanType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public bool $nullable = false;

    public function __construct(array $options = [], bool $nullable = false)
    {
        $this->nullable = $options['nullable'] ?? $nullable;
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\BooleanTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [];
    }

    public function getIsNullable(): bool
    {
        return $this->nullable;
    }
}
