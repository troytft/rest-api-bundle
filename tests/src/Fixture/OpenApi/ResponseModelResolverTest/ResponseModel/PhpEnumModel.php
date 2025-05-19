<?php

namespace Tests\Fixture\OpenApi\ResponseModelResolverTest\ResponseModel;

use Tests;
use RestApiBundle;

class PhpEnumModel implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getStringRequired(): Tests\Fixture\TestApp\Enum\PhpStringEnum
    {
        return Tests\Fixture\TestApp\Enum\PhpStringEnum::CREATED;
    }

    public function getStringNullable(): ?Tests\Fixture\TestApp\Enum\PhpStringEnum
    {
        return null;
    }
}
