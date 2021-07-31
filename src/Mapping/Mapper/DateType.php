<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

use function is_array;
use function is_string;

/**
 * @Annotation
 * @Target({"PROPERTY", "ANNOTATION"})
 */
#[\Attribute(\Attribute::TARGET_PROPERTY)]
class DateType extends RestApiBundle\Mapping\Mapper\BaseNullableType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    public function __construct(
        $options = [],
        private ?string $format = null,
        ?bool $nullable = null
    ) {
        if (is_string($options)) {
            $this->format = $options;
            parent::__construct(nullable: $nullable);
        } elseif (is_array($options)) {
            $this->format = $options['value'] ?? $options['format'] ?? $format;
            parent::__construct(nullable: $options['nullable'] ?? $nullable);

        } else {
            throw new \InvalidArgumentException();
        }
    }

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\DateTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION => $this->format,
        ];
    }
}
