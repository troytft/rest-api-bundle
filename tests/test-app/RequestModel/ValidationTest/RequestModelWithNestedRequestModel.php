<?php

namespace TestApp\RequestModel\ValidationTest;

use TestApp;
use RestApiBundle\Mapping\RequestModel as Mapping;

class RequestModelWithNestedRequestModel implements Mapping\RequestModelInterface
{
    /**
     * @var TestApp\RequestModel\ValidationTest\InnerRequestModel
     *
     * @Mapping\RequestModelType(class="TestApp\RequestModel\ValidationTest\InnerRequestModel")
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
