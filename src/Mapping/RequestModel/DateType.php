<?php

namespace RestApiBundle\Mapping\RequestModel;

use RestApiBundle;

/**
 * @Annotation
 */
class DateType implements RestApiBundle\Mapping\Mapper\ScalarTypeInterface
{
    use NullableTrait;

    public ?string $format = null;

    public function getTransformerClass(): string
    {
        return RestApiBundle\Services\Mapper\Transformer\DateTransformer::class;
    }

    public function getTransformerOptions(): array
    {
        return [
            RestApiBundle\Services\Mapper\Transformer\DateTransformer::FORMAT_OPTION_NAME => $this->format,
        ];
    }
}
