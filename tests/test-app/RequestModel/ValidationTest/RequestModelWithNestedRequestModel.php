<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\RequestModelInterface;
use RestApiBundle\Annotation\Request as Mapper;

class RequestModelWithNestedRequestModel implements RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel
     *
     * @Mapper\RequestModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel")
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
