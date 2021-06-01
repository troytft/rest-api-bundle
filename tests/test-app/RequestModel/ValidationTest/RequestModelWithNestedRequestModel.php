<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\Mapper as Mapper;

class RequestModelWithNestedRequestModel implements \RestApiBundle\Mapping\RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel
     *
     * @Mapper\ModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel")
     */
    private $nestedRequestModel;

    public function getNestedRequestModel(): TestApp\RequestModel\ValidationTest\InnerRequestModel
    {
        return $this->nestedRequestModel;
    }

    public function setNestedRequestModel(TestApp\RequestModel\ValidationTest\InnerRequestModel $nestedRequestModel)
    {
        $this->nestedRequestModel = $nestedRequestModel;

        return $this;
    }
}
