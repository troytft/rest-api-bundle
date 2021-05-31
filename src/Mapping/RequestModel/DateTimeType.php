<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class DateTimeType implements RestApiBundle\Mapping\Mapper\ScalarTypeInterface
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
            RestApiBundle\Services\Mapper\Transformer\DateTimeTransformer::FORMAT_OPTION_NAME => $this->format,
        ];
    }
}
