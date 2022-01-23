<?php

namespace Tests\Fixture\RequestModel\ValidationTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestUndefinedKeyModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?string $field;
}