<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;

class DateTimeType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getRequired(): \DateTime
    {
        return new \DateTime();
    }

    public function getNullable(): ?\DateTime
    {
        return null;
    }
}
