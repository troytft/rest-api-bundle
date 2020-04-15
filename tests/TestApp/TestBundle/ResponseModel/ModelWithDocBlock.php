<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use Tests;
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
     * @return Tests\TestApp\TestBundle\ResponseModel\CombinedModel
     */
    public function getModelField()
    {
        return new Tests\TestApp\TestBundle\ResponseModel\CombinedModel();
    }

    /**
     * @return Tests\TestApp\TestBundle\ResponseModel\CombinedModel[]
     */
    public function getArrayOfModelsField(): array
    {
        return [];
    }
}
