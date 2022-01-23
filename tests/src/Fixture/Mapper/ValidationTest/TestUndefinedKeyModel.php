<?php

namespace Tests\Fixture\Mapper\ValidationTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestUndefinedKeyModel implements Mapper\ModelInterface
{
    public ?string $field;
}
