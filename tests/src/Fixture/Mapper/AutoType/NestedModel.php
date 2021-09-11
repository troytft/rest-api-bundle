<?php

namespace Tests\Fixture\Mapper\AutoType;

use RestApiBundle\Mapping\Mapper as Mapper;

class NestedModel implements Mapper\ModelInterface
{
    /** @Mapper\Field */
    public string $string;
}
