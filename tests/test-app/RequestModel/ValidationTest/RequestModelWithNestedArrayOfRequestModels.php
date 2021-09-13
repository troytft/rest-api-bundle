<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\Mapper as Mapper;

class RequestModelWithNestedArrayOfRequestModels implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel[]
     *
     * @Mapper\Expose
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
