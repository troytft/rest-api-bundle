<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\RequestModel;

class RequestModelWithNestedRequestModel implements RequestModel\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel
     *
     * @RequestModel\RequestModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel")
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
