<?php

namespace Tests\Fixture\Mapper\DateTransformersTest;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class KeepTimezoneModel implements Mapper\ModelInterface
{
    #[Mapper\KeepTimezone]
    public \DateTime $field;
}
