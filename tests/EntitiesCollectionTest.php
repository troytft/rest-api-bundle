<?php

namespace Tests;

use RestApiBundle;
use Tests;

class EntitiesCollectionTest extends BaseBundleTestCase
{
    public function testSuccess()
    {
        $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithCollectionOfEntities();
        $this->getRequestModelManager()->handleRequest($model, [
            'fieldWithCollectionOfEntities' => [1, 2]
        ]);
        $this->assertIsArray($model->getFieldWithCollectionOfEntities());
        $this->assertCount(2, $model->getFieldWithCollectionOfEntities());
        $this->assertTrue($model->getFieldWithCollectionOfEntities()[0] instanceof Tests\DemoApp\DemoBundle\Entity\File);
        $this->assertSame($model->getFieldWithCollectionOfEntities()[0]->getId(), 1);
        $this->assertTrue($model->getFieldWithCollectionOfEntities()[1] instanceof Tests\DemoApp\DemoBundle\Entity\File);
        $this->assertSame($model->getFieldWithCollectionOfEntities()[1]->getId(), 2);
    }

    public function testOrder()
    {
        $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithCollectionOfEntities();
        $this->getRequestModelManager()->handleRequest($model, [
            'fieldWithCollectionOfEntities' => [2, 1]
        ]);
        $this->assertIsArray($model->getFieldWithCollectionOfEntities());
        $this->assertCount(2, $model->getFieldWithCollectionOfEntities());
        $this->assertTrue($model->getFieldWithCollectionOfEntities()[0] instanceof Tests\DemoApp\DemoBundle\Entity\File);
        $this->assertSame($model->getFieldWithCollectionOfEntities()[0]->getId(), 2);
        $this->assertTrue($model->getFieldWithCollectionOfEntities()[1] instanceof Tests\DemoApp\DemoBundle\Entity\File);
        $this->assertSame($model->getFieldWithCollectionOfEntities()[1]->getId(), 1);
    }

    public function testEntityNotFound()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithCollectionOfEntities();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithCollectionOfEntities' => [1, 2, 3]
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities' => ['One entity of entities collection not found.']], $exception->getProperties());
        }
    }

    public function testNull()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithCollectionOfEntities();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithCollectionOfEntities' => null
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities' => ['This value should be collection.']], $exception->getProperties());
        }
    }

    public function testInvalidItemType()
    {
        try {
            $model = new Tests\DemoApp\DemoBundle\RequestModel\ModelWithCollectionOfEntities();
            $this->getRequestModelManager()->handleRequest($model, [
                'fieldWithCollectionOfEntities' => [1, 'string']
            ]);
            $this->fail();
        } catch (RestApiBundle\Exception\RequestModelMappingException $exception) {
            $this->assertSame(['fieldWithCollectionOfEntities.1' => ['This value should be integer.']], $exception->getProperties());
        }
    }
}
