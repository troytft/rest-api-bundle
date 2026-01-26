<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;
use Tests;

class PolyfillEnumType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getStringRequired(): Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString
    {
        return Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::from(Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString::ARCHIVED);
    }

    public function getStringNullable(): ?Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString
    {
        return null;
    }

    /**
     * @return \Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString[]
     */
    public function getArrayRequired(): array
    {
        return [];
    }

    /**
     * @return \Tests\Fixture\TestApp\Enum\NamespaceExample\PolyfillString[]
     */
    public function getArrayNullable(): array
    {
        return [];
    }
}
