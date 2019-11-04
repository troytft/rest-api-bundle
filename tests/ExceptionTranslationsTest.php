<?php

namespace Tests;

use Tests;
use RestApiBundle;

class ExceptionTranslationsTest extends BaseBundleTestCase
{
    public function testBooleanRequiredException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'booleanType' => 'string',
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['booleanType' => ['This value should be a boolean.']], $exception->getProperties());
        }
    }

    public function testStringRequiredException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'stringType' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['stringType' => ['This value should be a string.']], $exception->getProperties());
        }
    }

    public function testIntegerRequiredException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'integerType' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['integerType' => ['This value should be an integer.']], $exception->getProperties());
        }
    }

    public function testFloatRequiredException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'floatType' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['floatType' => ['This value should be a float.']], $exception->getProperties());
        }
    }

    public function testModelRequiredException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'model' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['model' => ['This value should be an object.']], $exception->getProperties());
        }
    }

    public function testCollectionRequiredException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'collection' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['collection' => ['This value should be a collection.']], $exception->getProperties());
        }
    }

    public function testInvalidDateFormatException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'date' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['date' => ['This value should be valid date with format "Y-m-d".']], $exception->getProperties());
        }
    }

    public function testInvalidDateTimeFormatException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'dateTime' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['dateTime' => ['This value should be valid date time with format "Y-m-d\TH:i:sP".']], $exception->getProperties());
        }
    }

    public function testUndefinedKeyException()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithAllTypes();
            $this->getRequestModelManager()->handleRequest($model, [
                'undefinedKey' => false,
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['undefinedKey' => ['The key is not defined in the model.']], $exception->getProperties());
        }
    }
}
