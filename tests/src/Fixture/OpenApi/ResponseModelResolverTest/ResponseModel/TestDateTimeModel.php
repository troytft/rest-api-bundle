<?php

namespace Tests\Fixture\OpenApi\ResponseModelResolverTest\ResponseModel;

use RestApiBundle;

class TestDateTimeModel implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getField(): \DateTime
    {
        return new \DateTime();
    }
}
