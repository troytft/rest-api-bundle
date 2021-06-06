<?php

namespace RestApiBundle\Mapping\Mapper;

use RestApiBundle;

/**
 * @Annotation
 */
class DateTimeType implements RestApiBundle\Mapping\Mapper\TransformerAwareTypeInterface
{
    use NullableTrait;

    public ?string $format = null;

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
}
