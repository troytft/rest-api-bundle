<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\RequestModel as Mapping;

class RequestModelWithNestedArrayOfRequestModels implements Mapping\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel[]
     *
     * @Mapping\ArrayType(type=@Mapping\RequestModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel"))
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
