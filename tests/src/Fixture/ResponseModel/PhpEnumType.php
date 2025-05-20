<?php

declare(strict_types=1);

namespace Tests\Fixture\ResponseModel;

use RestApiBundle;
use Tests;

class PhpEnumType implements RestApiBundle\Mapping\ResponseModel\ResponseModelInterface
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
