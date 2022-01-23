<?php

namespace Tests\Fixture\Mapper\ValidationTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestUndefinedKeyModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?string $field;
}
