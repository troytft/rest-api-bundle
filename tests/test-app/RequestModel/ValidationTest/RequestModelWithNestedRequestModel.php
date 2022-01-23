<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\Mapper;

#[Mapper\ExposeAll]
class RequestModelWithNestedRequestModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    public TestApp\RequestModel\ValidationTest\ChildModel $childModel;
}
