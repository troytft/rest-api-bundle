<?php

namespace TestApp\ResponseModel;

use TestApp;
use RestApiBundle;

class ModelWithTypeHint implements RestApiBundle\ResponseModelInterface
{
    public function getStringField(): string
    {
        return 'string';
    }

    public function getNullableStringField(): ?string
    {
        return null;
    }

    public function getDateTimeField(): \DateTime
    {
        return new \DateTime();
    }

    public function getModelField(): TestApp\ResponseModel\CombinedModel
    {
        return new TestApp\ResponseModel\CombinedModel();
    }

    public function getDateType(): TestApp\Enum\StringEnum
    {
        return TestApp\Enum\StringEnum::from('first');
    }
}
