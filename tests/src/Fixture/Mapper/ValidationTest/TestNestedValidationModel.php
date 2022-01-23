<?php

namespace Tests\Fixture\Mapper\ValidationTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestNestedValidationModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?Tests\Fixture\Mapper\ValidationTest\ChildModel $childModel;

    /**
     * @var Tests\Fixture\Mapper\ValidationTest\ChildModel[]|null
     */
    public ?array $childModels;
}
