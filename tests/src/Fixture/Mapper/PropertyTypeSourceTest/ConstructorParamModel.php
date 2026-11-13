<?php

namespace Tests\Fixture\Mapper\PropertyTypeSourceTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class ConstructorParamModel implements Mapper\ModelInterface
{
    public ?string $field = null;

    public function __construct(int $field = 0)
    {
        \assert($field >= 0);
    }
}
