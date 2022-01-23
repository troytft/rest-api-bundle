<?php

namespace Tests\Fixture\RequestModel\ValidationTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestUndefinedKeyModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?string $field;
}