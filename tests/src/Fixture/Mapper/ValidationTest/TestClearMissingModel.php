<?php

namespace Tests\Fixture\Mapper\ValidationTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestClearMissingModel implements Mapper\ModelInterface
{
    public string $field = 'default value';
}
