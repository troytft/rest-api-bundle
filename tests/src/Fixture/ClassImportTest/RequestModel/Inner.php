<?php

namespace Tests\Fixture\ClassImportTest\RequestModel;

use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class Inner implements Mapper\ModelInterface
{
    public string $stringField;
}
