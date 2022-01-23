<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class RequestModelWithNestedArrayOfRequestModels implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\ChildModel[]
     */
    public array $childModels;
}
