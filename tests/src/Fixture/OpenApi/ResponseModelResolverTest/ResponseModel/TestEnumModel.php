<?php

namespace Tests\Fixture\OpenApi\ResponseModelResolverTest\ResponseModel;

use Tests;
use RestApiBundle;

class TestEnumModel implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getField(): Tests\Fixture\TestApp\Enum\PolyfillStringEnum
    {
        return Tests\Fixture\TestApp\Enum\PolyfillStringEnum::from(Tests\Fixture\TestApp\Enum\PolyfillStringEnum::ARCHIVED);
    }
}
