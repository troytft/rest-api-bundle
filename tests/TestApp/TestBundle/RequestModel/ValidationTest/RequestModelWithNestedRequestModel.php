<?php

namespace Tests\TestApp\TestBundle\RequestModel\ValidationTest;

use Tests;
use RestApiBundle\RequestModelInterface;
use RestApiBundle\Annotation\Request as Mapper;

class RequestModelWithNestedRequestModel implements RequestModelInterface
{
    /**
     * @var Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel
     *
     * @Mapper\RequestModelType(class="Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel")
     */
    private $nestedRequestModel;

    public function getNestedRequestModel(): Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel
    {
        return $this->nestedRequestModel;
    }

    public function setNestedRequestModel(Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel $nestedRequestModel)
    {
        $this->nestedRequestModel = $nestedRequestModel;

        return $this;
    }
}
