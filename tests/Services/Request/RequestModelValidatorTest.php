<?php

namespace Tests\Services\Request;

use Tests;
use RestApiBundle;
use function var_dump;

class RequestModelValidatorTest extends Tests\BaseBundleTestCase
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
    
    private function getRequestModelValidator(): RestApiBundle\Services\Request\RequestModelValidator
    {
        /** @var RestApiBundle\Services\Request\RequestModelValidator $result */
        $result = $this->getContainer()->get(RestApiBundle\Services\Request\RequestModelValidator::class);

        return $result;
    }
}
