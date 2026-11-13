<?php

namespace Tests\Fixture\Mapper\PropertyTypeSourceTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class AdderModel implements Mapper\ModelInterface
{
    public ?string $field = null;

    public function addField(int $field): void
    {
    }
}
