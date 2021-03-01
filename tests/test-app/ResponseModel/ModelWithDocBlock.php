<?php

namespace TestApp\ResponseModel;

use TestApp;
use RestApiBundle;

class ModelWithDocBlock implements RestApiBundle\ResponseModelInterface
{
    /**
     * @return string
     */
    public function getStringField()
    {
        return 'string';
    }

    /**
     * @return string|null
     */
    public function getNullableStringField()
    {
        return null;
    }

    /**
     * @return \DateTime
     */
    public function getDateTimeField()
    {
        return new \DateTime();
    }

    /**
     * @return TestApp\ResponseModel\CombinedModel
     */
    public function getModelField()
    {
        return new TestApp\ResponseModel\CombinedModel();
    }

    /**
     * @return TestApp\ResponseModel\CombinedModel[]
     */
    public function getArrayOfModelsField(): array
    {
        return [];
    }

    /**
     * @return TestApp\Enum\StringEnum
     */
    public function getDateType()
    {
        return TestApp\Enum\StringEnum::from('first');
    }
}
