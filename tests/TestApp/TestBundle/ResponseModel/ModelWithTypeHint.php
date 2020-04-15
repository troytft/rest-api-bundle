<?php

namespace Tests\TestApp\TestBundle\ResponseModel;

use Tests;
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

    /**
     * @return Tests\TestApp\TestBundle\ResponseModel\CombinedModel
     */
    public function getModelField()
    {
        return new Tests\TestApp\TestBundle\ResponseModel\CombinedModel();
    }
}
