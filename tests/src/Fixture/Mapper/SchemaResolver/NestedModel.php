<?php

namespace Tests\Fixture\Mapper\SchemaResolver;

use RestApiBundle\Mapping\Mapper as Mapper;

class NestedModel implements Mapper\ModelInterface
{
    /** @Mapper\Expose */
    public string $string;
}
