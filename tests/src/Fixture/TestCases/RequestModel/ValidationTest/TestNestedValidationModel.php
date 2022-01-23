<?php

namespace Tests\Fixture\TestCases\RequestModel\ValidationTest;

use Tests;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class TestNestedValidationModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var Tests\Fixture\TestCases\RequestModel\ValidationTest\ChildModel[]|null
     */
    public ?array $childModels;

    public ?Tests\Fixture\TestCases\RequestModel\ValidationTest\ChildModel $childModel;
}
