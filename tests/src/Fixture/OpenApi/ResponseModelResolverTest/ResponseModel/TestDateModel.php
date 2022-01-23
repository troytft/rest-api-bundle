<?php

namespace Tests\Fixture\OpenApi\ResponseModelResolverTest\ResponseModel;

use RestApiBundle;

class TestDateModel implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getField(): RestApiBundle\Mapping\ResponseModel\Date
    {
        return RestApiBundle\Mapping\ResponseModel\Date::from(new \DateTime());
    }
}
