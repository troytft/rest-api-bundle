<?php

namespace RestApiBundle\Model\Mapper\Types;

use RestApiBundle;

class DateType extends RestApiBundle\Model\Mapper\Types\BaseNullableType implements RestApiBundle\Model\Mapper\Types\TransformerAwareTypeInterface
{
    public function __construct(
        public ?string $format = null,
        ?bool $nullable = null
    ) {
        parent::__construct(nullable: $nullable);
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
