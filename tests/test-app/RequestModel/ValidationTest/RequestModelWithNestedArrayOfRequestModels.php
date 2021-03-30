<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\RequestModel\RequestModelInterface;
use RestApiBundle\Annotation\Request as Mapper;

class RequestModelWithNestedArrayOfRequestModels implements RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel[]
     *
     * @Mapper\ArrayType(type=@Mapper\RequestModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel"))
     */
    private $nestedRequestModels;

    /**
     * @return TestApp\RequestModel\ValidationTest\InnerRequestModel[]
     */
    public function getNestedRequestModels(): array
    {
        return $this->nestedRequestModels;
    }

    /**
     * @param TestApp\RequestModel\ValidationTest\InnerRequestModel[] $nestedRequestModels
     *
     * @return $this
     */
    public function setNestedRequestModels(array $nestedRequestModels)
    {
        $this->nestedRequestModels = $nestedRequestModels;

        return $this;
    }
}
