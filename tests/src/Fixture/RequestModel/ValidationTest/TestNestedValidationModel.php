<?php

namespace Tests\Fixture\RequestModel\ValidationTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestNestedValidationModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public ?Tests\Fixture\RequestModel\ValidationTest\ChildModel $childModel;

    /**
     * @var Tests\Fixture\RequestModel\ValidationTest\ChildModel[]|null
     */
    public ?array $childModels;
}