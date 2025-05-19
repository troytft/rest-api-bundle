<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;

class DateType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getRequired(): RestApiBundle\Mapping\ResponseModel\Date
    {
        return RestApiBundle\Mapping\ResponseModel\Date::from(new \DateTime());
    }

    public function getNullable(): ?RestApiBundle\Mapping\ResponseModel\Date
    {
        return null;
    }
}
