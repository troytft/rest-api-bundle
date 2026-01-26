<?php

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;
use Tests;

class PhpEnumType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
{
    public function getStringRequired(): Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString
    {
        return Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString::CREATED;
    }

    public function getStringNullable(): ?Tests\Fixture\TestApp\Enum\NamespaceExample\PhpString
    {
        return null;
    }
}
