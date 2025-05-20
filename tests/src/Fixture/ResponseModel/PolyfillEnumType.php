<?php

namespace Tests\Fixture\ResponseModel;

use Tests;
use RestApiBundle;

class PolyfillEnumType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getStringRequired(): Tests\Fixture\TestApp\Enum\PolyfillStringEnum
    {
        return Tests\Fixture\TestApp\Enum\PolyfillStringEnum::from(Tests\Fixture\TestApp\Enum\PolyfillStringEnum::ARCHIVED);
    }

    public function getStringNullable(): ?Tests\Fixture\TestApp\Enum\PolyfillStringEnum
    {
        return null;
    }

    /**
     * @return Tests\Fixture\TestApp\Enum\PolyfillStringEnum[]
     */
    public function getArrayRequired(): array
    {
        return [];
    }

    /**
     * @return Tests\Fixture\TestApp\Enum\PolyfillStringEnum[]
     */
    public function getArrayNullable(): array
    {
        return [];
    }
}
