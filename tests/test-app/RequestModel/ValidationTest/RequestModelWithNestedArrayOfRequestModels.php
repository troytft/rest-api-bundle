<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\RequestModel;

class RequestModelWithNestedArrayOfRequestModels implements RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel[]
     *
     * @RequestModel\ArrayType(type=@RequestModel\RequestModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel"))
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
