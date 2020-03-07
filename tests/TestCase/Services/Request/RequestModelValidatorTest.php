<?php

namespace Tests\TestCase\Services\Request;

use Tests;

class RequestModelValidatorTest extends Tests\TestCase\BaseTestCase
{
    public function testNestedRequestModel()
    {
        $innerRequestModel = new Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel();
        $requestModel = new Tests\TestApp\TestBundle\RequestModel\ValidationTest\RequestModelWithNestedRequestModel();
        $requestModel->setNestedRequestModel($innerRequestModel);

        $value = $this->getRequestModelValidator()->validate($requestModel);

        $this->assertCount(1, $value);
        $this->assertArrayHasKey('nestedRequestModel.field', $value);
        $this->assertSame(['Invalid value.'], $value['nestedRequestModel.field']);
    }

    public function testNestedArrayOfRequestModels()
    {
        $requestModel = new Tests\TestApp\TestBundle\RequestModel\ValidationTest\RequestModelWithNestedArrayOfRequestModels();
        $requestModel->setNestedRequestModels([
            new Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel(),
            new Tests\TestApp\TestBundle\RequestModel\ValidationTest\InnerRequestModel(),
        ]);

        $value = $this->getRequestModelValidator()->validate($requestModel);

        $this->assertCount(2, $value);
        $this->assertArrayHasKey('nestedRequestModels.0.field', $value);
        $this->assertArrayHasKey('nestedRequestModels.1.field', $value);
        $this->assertSame(['Invalid value.'], $value['nestedRequestModels.0.field']);
        $this->assertSame(['Invalid value.'], $value['nestedRequestModels.1.field']);
    }
}
