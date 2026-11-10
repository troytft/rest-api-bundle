<?php

namespace Tests\Fixture\Mapper\DateTransformersTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class DefaultTimezoneModel implements Mapper\ModelInterface
{
    public \DateTime $field;
}
