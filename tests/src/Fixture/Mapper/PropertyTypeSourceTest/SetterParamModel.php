<?php

namespace Tests\Fixture\Mapper\PropertyTypeSourceTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class SetterParamModel implements Mapper\ModelInterface
{
    public ?string $field = null;

    /**
     * @param string $field
     */
    public function setField($field): void
    {
        $this->field = $field;
    }
}
