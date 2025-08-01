<?php

namespace Tests\Fixture\Mapper\DateTimeTransformerTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Model implements Mapper\ModelInterface
{
    public \DateTime $dateTime;
}
