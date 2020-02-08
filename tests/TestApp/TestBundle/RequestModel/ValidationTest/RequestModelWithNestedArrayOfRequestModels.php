<?php

namespace Tests\TestApp\TestBundle\RequestModel\ValidationTest;

use Tests;
use RestApiBundle\RequestModelInterface;
use RestApiBundle\Annotation\Request as Mapper;

class RequestModelWithNestedArrayOfRequestModels implements RequestModelInterface
{
    /**
     * @var Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel[]
     *
     * @Mapper\ArrayType(type=@Mapper\RequestModelType(class="Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel"))
     */
    private $nestedRequestModels;

    /**
     * @return Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel[]
     */
    public function getNestedRequestModels(): array
    {
        return $this->nestedRequestModels;
    }

    /**
     * @param Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel[] $nestedRequestModels
     *
     * @return $this
     */
    public function setNestedRequestModels(array $nestedRequestModels)
    {
        $this->nestedRequestModels = $nestedRequestModels;

        return $this;
    }
}
