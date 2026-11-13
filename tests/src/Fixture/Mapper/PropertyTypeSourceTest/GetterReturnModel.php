<?php

namespace Tests\Fixture\Mapper\PropertyTypeSourceTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class GetterReturnModel implements Mapper\ModelInterface
{
    public ?string $field = null;

    /**
     * @return string
     */
    public function getField()
    {
        return (string) $this->field;
    }
}
